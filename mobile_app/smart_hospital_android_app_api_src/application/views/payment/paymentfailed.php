
<?php if($this->session->flashdata('error')){

print_r($this->session->flashdata('error'));

}else{
	echo "You have cancelled this payment.";
} ?>