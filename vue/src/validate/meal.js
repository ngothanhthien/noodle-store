export default function (form){
    if(form.name==""){
        throw "Tên không được để trống";
    }
    if(form.price==""){
        throw "Giá tiền không được để trống";
    }
}