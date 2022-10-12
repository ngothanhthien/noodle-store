export default function ({address,phone,payment_gate}){
    if(payment_gate==''){
        throw "Phải có phương thức mua";
    }
    if(payment_gate==2){//Đặt qua điện thoại
        if(address==''||phone==''){
            throw "Khi mua qua điện thoại phải có địa chỉ và số điện thoại";
        }
    }
    if((address!=''&&phone=='')||(address==''&&phone!='')){
        throw "Địa chỉ và số điện thoại phải cùng điền hoặc cùng không điền";
    }
}