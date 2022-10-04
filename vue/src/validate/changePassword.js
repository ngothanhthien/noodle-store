export default function ({oldPassword, newPassword,confirmPassword}){
    if(oldPassword==""){
        throw "Phải có mật khẩu cũ";
    }
    if(newPassword==""){
        throw "Phải có mật khẩu mới";
    }
    if(confirmPassword.localeCompare(newPassword)!=0){
        throw "Mật khẩu xác nhận không giống"
    }
}