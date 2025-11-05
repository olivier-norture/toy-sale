function sleep(ms){
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function imprimer_page(){
    // Prepare page for printing
    hideNotPrintableElements();
    hideOnlyPrintableElements(false);
    changeTabHeaderWidth(100);
    removeBorder();
    initTextArea();
    changeFontSize(12);
    
    // Apply print-specific optimizations
    optimizePrintLayout();
    
    // Add content grouping for better page breaks
    addPrintContentGrouping();
    
    // Analyze and optimize table layout
    optimizeTableForPrint();
    
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

/**
 * Optimize the page layout for printing by applying print-specific classes
 * and ensuring proper content grouping
 */
function optimizePrintLayout() {
    // Add print-specific classes to key elements
    var header = document.querySelector('.onlyPrintable');
    if (header) {
        header.classList.add('print-header');
    }
    
    var participantInfo = document.querySelector('#contact_info');
    if (participantInfo) {
        participantInfo.classList.add('print-participant-info');
    }
    
    var footer = document.querySelectorAll('.onlyPrintable')[1]; // Second onlyPrintable is footer
    if (footer) {
        footer.classList.add('print-footer');
    }
    
    // Ensure table has proper print classes
    var table = document.querySelector('table');
    if (table) {
        table.classList.add('print-table-section');
    }
}

/**
 * Add content grouping wrappers to ensure proper page breaks
 */
function addPrintContentGrouping() {
    var mainContent = document.querySelector('#main_content');
    if (!mainContent) return;
    
    // Create header group (header + participant info + some table content)
    var headerGroup = document.createElement('div');
    headerGroup.className = 'print-header-group';
    
    // Move header and participant info to header group
    var header = document.querySelector('.onlyPrintable');
    var participantInfo = document.querySelector('#contact_info');
    var billNumber = document.querySelector('.labelInfoTitle');
    
    if (header) {
        headerGroup.appendChild(header.cloneNode(true));
        header.style.display = 'none';
    }
    
    if (billNumber) {
        headerGroup.appendChild(billNumber.cloneNode(true));
    }
    
    if (participantInfo) {
        var participantClone = participantInfo.cloneNode(true);
        // Remove form elements from participant info clone
        var forms = participantClone.querySelectorAll('form, .notPrintable');
        forms.forEach(function(form) {
            form.remove();
        });
        headerGroup.appendChild(participantClone);
    }
    
    // Insert header group at the beginning of main content
    mainContent.insertBefore(headerGroup, mainContent.firstChild);
    
    // Create footer group (summary + footer)
    var footerGroup = document.createElement('div');
    footerGroup.className = 'print-footer-group';
    
    var footer = document.querySelectorAll('.onlyPrintable')[1];
    if (footer) {
        footerGroup.appendChild(footer.cloneNode(true));
        footer.style.display = 'none';
    }
    
    // Add total row to footer group
    var totalRow = document.querySelector('table tr:last-child');
    if (totalRow) {
        var totalClone = totalRow.cloneNode(true);
        var totalTable = document.createElement('table');
        totalTable.appendChild(totalClone);
        footerGroup.insertBefore(totalTable, footerGroup.firstChild);
    }
    
    // Append footer group at the end
    mainContent.appendChild(footerGroup);
}

/**
 * Optimize table layout for printing by grouping rows and preventing bad breaks
 */
function optimizeTableForPrint() {
    var table = document.querySelector('table');
    if (!table) return;
    
    var rows = table.querySelectorAll('tr');
    if (rows.length <= 2) return; // Header + total row only
    
    // Add print-table-header class to header row
    if (rows[0]) {
        rows[0].classList.add('print-table-header');
    }
    
    // Group table rows in chunks to prevent orphaned content
    var chunkSize = calculateOptimalChunkSize(rows.length);
    var currentChunk = null;
    var chunkRowCount = 0;
    
    for (var i = 1; i < rows.length - 1; i++) { // Skip header and total rows
        if (chunkRowCount === 0) {
            // Create new chunk
            currentChunk = document.createElement('tbody');
            currentChunk.className = 'print-table-chunk';
            rows[i].parentNode.insertBefore(currentChunk, rows[i]);
        }
        
        // Move row to current chunk
        currentChunk.appendChild(rows[i]);
        chunkRowCount++;
        
        if (chunkRowCount >= chunkSize) {
            chunkRowCount = 0;
            currentChunk = null;
        }
    }
    
    // Ensure textareas are properly sized for print
    var textareas = table.querySelectorAll('textarea');
    textareas.forEach(function(textarea) {
        // Calculate content height and set appropriate height
        var contentHeight = Math.max(20, textarea.scrollHeight);
        textarea.style.height = contentHeight + 'px';
        textarea.style.minHeight = '20px';
        textarea.style.maxHeight = '100px'; // Prevent excessive height
    });
}

/**
 * Calculate optimal chunk size for table rows based on total number of rows
 * @param {number} totalRows - Total number of data rows (excluding header/footer)
 * @returns {number} Optimal chunk size
 */
function calculateOptimalChunkSize(totalRows) {
    if (totalRows <= 10) return totalRows; // Small tables don't need chunking
    if (totalRows <= 30) return 8; // Medium tables: 8 rows per chunk
    if (totalRows <= 60) return 12; // Large tables: 12 rows per chunk
    return 15; // Very large tables: 15 rows per chunk
}

/**
 * Calculate estimated page height needed for content
 * @param {Element} element - Element to measure
 * @returns {number} Estimated height in pixels
 */
function calculateContentHeight(element) {
    if (!element) return 0;
    
    var style = window.getComputedStyle(element);
    var height = element.offsetHeight;
    var marginTop = parseInt(style.marginTop) || 0;
    var marginBottom = parseInt(style.marginBottom) || 0;
    
    return height + marginTop + marginBottom;
}

/**
 * Ensure minimum content accompanies headers and footers
 */
function ensureMinimumContentGrouping() {
    var A4_HEIGHT_PX = 1123; // Approximate A4 height in pixels at 96 DPI
    var MIN_CONTENT_HEIGHT = A4_HEIGHT_PX * 0.3; // 30% of page height
    
    var headerGroup = document.querySelector('.print-header-group');
    var footerGroup = document.querySelector('.print-footer-group');
    
    if (headerGroup) {
        var headerHeight = calculateContentHeight(headerGroup);
        if (headerHeight < MIN_CONTENT_HEIGHT) {
            // Add more content to header group if needed
            var table = document.querySelector('table');
            if (table) {
                var firstChunk = table.querySelector('.print-table-chunk');
                if (firstChunk) {
                    headerGroup.appendChild(firstChunk.cloneNode(true));
                }
            }
        }
    }
    
    if (footerGroup) {
        var footerHeight = calculateContentHeight(footerGroup);
        if (footerHeight < MIN_CONTENT_HEIGHT) {
            // Ensure footer has enough preceding content
            footerGroup.style.pageBreakBefore = 'avoid';
            footerGroup.style.breakBefore = 'avoid';
        }
    }
}
