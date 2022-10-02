const server="http://127.0.0.1:8000/api";
export const loginAPI=server+"/user/login"; //post
export const getInfoTokenAPI=server+"/me"; //get
export const logoutAPI=server+"/logout"; //get
export const getAllStaffAPI=server+"/users"; //get
export const staffAPI=server+"/user"; //delete +id,post
export const getAllMealAPI=server+"/meals"; //get
export const getCustomerByPhone=server+"/customer";//get
export const getOrders=server+"/orders/"; //get +state
export const orderAPI=server+"/order/"; //post, get+id,
export const orderStateChange=
{
    success: server+"/order/success/",
    fail:   server+"/order/fail/",
    cancel:   server+"/order/cancel/",
}; //patch+id
export const mealAPI=server+"/meal";//post