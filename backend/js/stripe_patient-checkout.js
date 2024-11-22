// Get Your Stripe Publishable API Key
let publishable_key = document.querySelector("#publishable_key").value;

// Set your Stripe publishable API key to an instance of Stripe Object
const stripe = Stripe(publishable_key);

// Define the card elements variable
let elements;

// Select a payment form element
const paymentForm = document.querySelector("#stripe-payment-form");

// Select a payment submit button
const submitButton = document.querySelector("#submit-button");
let button_loading=$('#submit-button');

// Select a payment submit button text
const submitText = document.querySelector("#submit-text");

// Select a payment submit spinner
// const spinner = document.querySelector("#spinner");

// Select a payment message response element
const messageContainer = document.querySelector("#stripe-payment-message");

// Select a payment processing element
const paymentProcessing = document.querySelector("#payment_processing");

// Select a payment reinitiate element
const payReinitiate = document.querySelector("#payment-reinitiate");

// Get a payment_intent_client_secret parameter from URL
const checkClientSecret = new URLSearchParams(window.location.search).get(
    "payment_intent_client_secret"
);

stripeProcessing(true);
// Check is payment_intent_client_secret parameter exist in the URL
if(!checkClientSecret){
    stripeProcessing(false);
    // Create a new instance of Stripe Elements UI library and attach the client secret to it
    initialize();
}

// Check the status of PaymentIntent creation
checkStatus();

// Attach a payment submit event handler to payment form
paymentForm.addEventListener("submit", handlePaymentSubmit);

// Fetch a payment intent and capture the client secret
let payment_intent_id;
async function initialize(event) {
    const amount = document.getElementById("amount").value;
    const currency = document.getElementById("currency").value;

    const { paymentIntentId, clientSecret } = await fetch(baseurl+"patient/payment/stripe//create_payment_intent", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ currency: currency, amount: amount}),
    }).then((r) => r.json());
    
    const appearance = {
        theme: 'stripe',
        rules: {
            '.Label': {
                fontWeight: 'bold',
            }
        }
    };
    
    elements = stripe.elements({ clientSecret, appearance });
    
    const paymentElement = elements.create("payment");
    paymentElement.mount("#stripe-payment-element");
    
    payment_intent_id = paymentIntentId;
}

// Function to handle payment form submit
async function handlePaymentSubmit(e) {
    e.preventDefault();
    setLoading(true);
    

    const { customer_id } = await fetch(baseurl+"patient/payment/stripe/create_customer", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ payment_intent_id: payment_intent_id }),
    }).then((r) => r.json());
    
    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            // Make sure to change this to your payment completion page
            return_url: window.location.href+'?customer_id='+customer_id,
        },
    });
    
    // This point will only be reached if there is an immediate error when
    // confirming the payment. Otherwise, your customer will be redirected to
    // your `return_url`. For some payment methods like iDEAL, your customer will
    // be redirected to an intermediate site first to authorize the payment, then
    // redirected to the `return_url`.
    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
        // paymentReinitiate();
    } else {
        showMessage("An unexpected error occured.");
        // paymentReinitiate();
    }
    setLoading(false);
}

// Fetch the PaymentIntent status after payment submission
async function checkStatus() {
    const clientSecret = new URLSearchParams(window.location.search).get(
        "payment_intent_client_secret"
    );
    
    const customer_id = new URLSearchParams(window.location.search).get(
        "customer_id"
    );
    
    if (!clientSecret) {
        return;
    }
    
    $('.button-between').fadeOut();
    $('.modal_inner_loader2').fadeIn("slow");
    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
    
    if (paymentIntent) {
        switch (paymentIntent.status) { 
            case "succeeded":
                // Post the transaction data to the server-side script
                // and redirect to the payment status page
                fetch(baseurl+"patient/payment/stripe/insert_payment", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ payment_intent: paymentIntent, customer_id: customer_id }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        window.location.href = data.return_url;
                    } else {
                        showMessage(data.error);
                        paymentReinitiate();
                        showButton();
                    }
                })
                .catch(console.error);
                break;
            case "processing":
                showMessage("Your payment is processing.");
                paymentReinitiate();
                showButton();
                break;
            case "requires_payment_method":
                showMessage("Your payment was not successful, please try again.");
                paymentReinitiate();
                showButton();
                break;
            default:
                showMessage("Something went wrong.");
                paymentReinitiate();
                showButton();
                break;
        }
    } else {
        showMessage("Something went wrong.");
        paymentReinitiate();
        showButton();
    }
  
}

// Display message
function showButton() {
    $('.button-between').fadeIn();
    $('.modal_inner_loader2').fadeOut("slow");
}
function showMessage(messageText) {
    errorMsg(messageText);
}

// Show a spinner on payment submission
function setLoading(isLoading) {
    if (isLoading) {
        // Disable the submit button and show a spinner
        button_loading.button('loading');
       
    } else {
        // Enable the submit button and hide a spinner
        button_loading.button('reset');
    
    }
}

// Show/Hide a spinner of processing on payment form
function stripeProcessing(isProcessing) {
    // if (isProcessing) {
    //     paymentForm.classList.add("hidden");
    //     paymentProcessing.classList.remove("hidden");
    // } else {
    //     paymentForm.classList.remove("hidden");
    //     paymentProcessing.classList.add("hidden");
    // }
}

// Display the payment reinitiate button
function paymentReinitiate() {
  
    payReinitiate.classList.remove("hidden");
    submitButton.classList.add("hidden");
   
}

// Reinitiate stripe payment
function reinitiateStripe() {
    window.location.href=window.location.href.split('?')[0];
}