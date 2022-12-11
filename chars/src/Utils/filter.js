filterSelection("all")

function filterSelection(c) {
    
    if (document.getElementsByClassName('active')[0]) document.getElementsByClassName('active')[0].className = "btn";
    if (document.getElementById(c + "-button")) document.getElementById(c + "-button").className = "btn active";

    var x, i;
    x = document.getElementsByClassName("filterDiv");
    // Add the "show" class (display:block) to the filtered elements, and remove the "show" class from the elements that are not selected
    for (i = 0; i < x.length; i++) {
        w3RemoveClass(x[i], "show");
        // wrap class names in spaces to prevent matching prefixes or suffixes
        if (c === "all" || (' ' + x[i].className + ' ').indexOf(' ' + c + ' ') > -1) {
            w3AddClass(x[i], "show");
        }
    }
}

// Show filtered elements
function w3AddClass(element, name) {
    var i, arr1, arr2;
    arr1 = element.className.split(" ");
    arr2 = name.split(" ");
    for (i = 0; i < arr2.length; i++) {
        if (arr1.indexOf(arr2[i]) == -1) {
            element.className += " " + arr2[i];
        }
    }
}

// Hide elements that are not selected
function w3RemoveClass(element, name) {
    var i, arr1, arr2;
    arr1 = element.className.split(" ");
    arr2 = name.split(" ");
    for (i = 0; i < arr2.length; i++) {
        while (arr1.indexOf(arr2[i]) > -1) {
            arr1.splice(arr1.indexOf(arr2[i]), 1);
        }
    }
    element.className = arr1.join(" ");
}