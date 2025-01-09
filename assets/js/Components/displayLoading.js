class Loading {    
    constructor() {
    }
    
    display (element) {
        element.innerHTML = '<div class="d-flex justify-content-center"> <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div></div>';
    }
}
 
export default Loading;