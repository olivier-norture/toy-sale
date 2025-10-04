function sleep(ms){
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function imprimer_page(){
    hideNotPrintableElements();
    hideOnlyPrintableElements(false);
    changeTabHeaderWidth(100);
    removeBorder();
    initTextArea();
    changeFontSize(12);
    window.print();
    await sleep(3000);
}

function cancel_print(){
    hideNotPrintableElements(false);
    hideOnlyPrintableElements(true);
}

/**
 * Hide all elements which are in "notPrintable"'s class members
 * These elements will not be printed with the print function
 * @param bool true to hide them, false to show them
 * @returns void
 */
function hideNotPrintableElements(bool = true){
    var value = "";
    if(bool){
        value = "none";
    }
    hideElementsClass(".notPrintable", value);
}

/**
 * Hide all elements which are in "hideOnlyPrintableElements"'s class members
 * These elements will only be shown when the page will be printed with the print function
 * @param bool true to hide them, false to show them
 * @returns void
 */
function hideOnlyPrintableElements(bool = true){
    var value = "block";
    if(bool){
        value = "none";
    }
    hideElementsClass(".onlyPrintable", value);
}

/**
 * Aplly the given display value to the given css's class
 * Use this to hide (display=none) or to show (display=block) all elements
 * of a class.
 * @param className the css's class to modify
 * @param display the display value to apply (block, none)
 * @returns void
 */
function hideElementsClass(className, display){
    var myElements = document.querySelectorAll(className);
    for (var i = 0; i < myElements.length; i++) {
        myElements[i].style.display = display;
    }
}

/**
 * Calculate the left to pay of the current bill and set the value inside of
 * the form in the webpage.
 * @returns void
 */
function leftToPay(){
    var tmp = document.getElementsByName("pay_basket_sum")[0].value - document.getElementsByName("pay_cash")[0].value - document.getElementsByName("pay_check")[0].value - document.getElementsByName("pay_credit_card")[0].value;
    document.getElementsByName("pay_left_to")[0].value = tmp > 0 ? tmp : 0;
}

/**
 * Calculate the give to back of the current bill and set the value inside of
 * the form in the webpage.
 * @returns {undefined}
 */
function toGiveBack(){
    var tmp = document.getElementsByName("pay_basket_sum")[0].value - document.getElementsByName("pay_cash")[0].value - document.getElementsByName("pay_check")[0].value - document.getElementsByName("pay_credit_card")[0].value;
    document.getElementsByName("pay_give_back")[0].value = tmp < 0 ? Math.abs(tmp) : 0;
}

/**
 * Calculate the left to pay and the give to back of the current bill.
 * @returns void
 */
function modifyPay(){
    leftToPay();
    toGiveBack();
}

/**
 * Fit the size of textarea with it's content
 * @param fieldId the textarea's id
 * @returns void
 */
function setTextareaHeight(textarea){
    textarea.style.height = "5px";
    textarea.style.height = textarea.scrollHeight+'px';
}

function initTextArea(){
    var list = document.getElementsByClassName("textarea");
    for(var i = 0; i < list.length; i++){
        list[i].style.width = '100%';
        setTextareaHeight(list[i]);
    }
}

/**
 * Removes the page border
 */
function removeBorder(){
    var list = document.getElementsByClassName("container");
    for(var i = 0; i < list.length; i++){
        list[i].style.marginLeft = "0px";
        list[i].style.marginRight = "0px";
    }
}

/**
 * Apply the given size to all font un the current page
 * @param int size
 */
function changeFontSize(size){
    //Content in input form
    var list = document.getElementsByTagName("input");
    for(var i = 0; i < list.length; i++){
        list[i].style.fontSize = size + "px";
    }
    
    //Content in textarea
    var list = document.getElementsByTagName("textarea");
    for(var i = 0; i < list.length; i++){
        list[i].style.fontSize = size + "px";
    }
    
    //All others
    document.getElementById("main_content").style.fontSize = size + "px";
}

function changeTabHeaderWidth(size){
    var list = document.getElementsByClassName("reference");
    for(var i = 0; i < list.length; i++){
        list[i].style.width = size + "px";
    }
    
    var list = document.getElementsByClassName("prix");
    for(var i = 0; i < list.length; i++){
        list[i].style.width = size + "px";
    }
}
