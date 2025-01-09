class Response {    
    handle (response, element) {
        switch(response.code) {
            case 'ADDED_SUCCESSFULLY':
                element.innerHTML = response.html;
                break;
            case 'INVALID_FORM':
                handleErrors(response.errors);
                break;
        }
    }
}
 
export default Response;