const server="http://127.0.0.1:8000/api";
export const loginAPI=server+"/user/login"; //post
export const getInfoTokenAPI=server+"/me"; //get
export const logoutAPI=server+"/logout"; //get
export const getAllStaffAPI=server+"/users"; //get
export const staffAPI=server+"/user"; //delete +id,post