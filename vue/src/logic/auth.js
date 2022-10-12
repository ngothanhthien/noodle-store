import router from '../router';
import Cookies from 'js-cookie'
export const backToLogin=()=>{
    Cookies.remove('User Token');
    Cookies.remove('User Info');
    router.push({name:'login'});
}
export const getUserToken=()=> Cookies.get('User Token');
export const getUserInfo=()=> Cookies.get('User Info')?JSON.parse(Cookies.get('User Info')):undefined;