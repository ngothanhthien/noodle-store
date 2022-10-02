export default function ({account,password}){
    if(account==""){
        throw "Tài khoản không được để trống";
    }
    if(password==""){
        throw "Mật khẩu không được để trống";
    }
}