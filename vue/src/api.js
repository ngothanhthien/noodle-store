const server="http://127.0.0.1:8000/api";
export const loginAPI=server+"/user/login"; //post
export const adminLoginAPI=server+"/admin/login"; //post
export const getInfoTokenAPI=server+"/me"; //get
export const logoutAPI=server+"/logout"; //get
export const getAllStaffAPI=server+"/users"; //get
export const getNewPasswordAPI=(id)=>server+"/user/"+id+"/new-password"; //patch
export const staffAPI=server+"/user"; //delete +id,post, get + id,PUT+id
export const getAllMealAPI=server+"/meals"; //get
export const getAllToppingAPI=server+"/meals/topping"; //get
export const getToppingByMeal=(id)=>server+"/meal/"+id+"/toppings";//get
export const getCustomerByPhone=server+"/customer";//get
export const getAllCustomerAPI=server+"/customers";//get
export const getOrders=server+"/orders/"; //get +state
export const orderAPI=server+"/order/"; //post, get+id,
export const orderStateChange=
{
    success: server+"/order/success/",
    fail:   server+"/order/fail/",
    cancel:   server+"/order/cancel/",
}; //patch+id
export const mealAPI=server+"/meal";//post,delete+id,put+id
export const changePasswordAPI=server+"/change-password";//patch