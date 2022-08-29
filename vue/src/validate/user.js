export const usernameValidate=(field)=>{
    if(field==""){
        throw "Tên đăng nhập không được để trống";
    }
}
export const passwordValidate=(field)=>{
    if(field==""){
        throw "Mật khẩu không được để trống";
    }
}