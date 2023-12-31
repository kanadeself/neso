var idolNesoSpace = document.getElementById('idolNesoSpace');
var ownedNesosSpace = document.getElementById('ownedNesoSpace');
var idolButtons = document.getElementsByClassName('collapsible');
var currentIdol = document.getElementById('currentIdol');
var currentIdolIcon = document.getElementById('currentIdolIcon');
var idolNameHeader = document.getElementById('idolnameheader');
var sizes = document.getElementById('sizes');
var filterContainer = document.getElementById('filterContainer');
var emptySpace = document.querySelectorAll('.startmsg');
var selectedSizeButton = document.getElementById('selectedSize');
var selectedIdolButton = document.getElementById('selectedIdol');
var totalContainer = document.getElementById('totalCounter');
var divCounters = document.getElementById('divCounters');
var headerdiv = document.getElementById("headerdiv");
var franchisePicker = document.getElementById("franchisePicker");
var idolSelectors = document.getElementById("idolSelectors");
var idolSelector = document.getElementById("idolselectordropdown");
var sizeSelector = document.getElementById("sizeFilterContainer");

function loadIdolSelectors() {
    var franchise = franchisePicker.options[franchisePicker.selectedIndex].value;
    $.ajax({
        url: '/src/getIdols.php',
        type: 'POST',
        data: function () {
            var data = new FormData();
            data.append('franchise', franchise);
            data.append('preflang', jsLang);
            return data;
        }(),
        success: function (data) {
            onIdolsLoaded(data);
        },
        error: function (data) {
            console.log("An error occurred when attempting to load Nesos: " + data);
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function hookIdolSelectors() {
    for (var i = 0; i < idolButtons.length; i++) {
        if (idolButtons[i].id.startsWith("idol_")) {
            idolButtons[i].onclick = function () {
                divCounters.style.display = "none";
                ownedNesosSpace.style.display = "none";
                totalContainer.style.display = "none";
                idolSelector.style.display = "none";
                sizeSelector.style.display = "flex";

                var fullName = this.getAttribute("full_name");
                var words = fullName.split(" ");
                var fullNamejoin = words.join("").toLowerCase();

                idolNesoSpace.style.display = "flex";

                $.ajax({
                    url: '/src/getNesos.php',
                    type: 'POST',
                    data: function () {
                        var data = new FormData();
                        data.append('FullName', fullNamejoin);
                        data.append('varLang', jsLang);
                        return data;
                    }(),
                    success: function (data) {
                        onNesosLoaded(data, false);
                    },
                    error: function (data) {
                        console.log("An error occurred when attempting to load Nesos: " + data);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });

                idolNameHeader.style.display = "flex";
                currentIdolIcon.style.display = "inline-block";
                currentIdolIcon.src = "/src/img/" + fullNamejoin + '/icon.png';

                if (jsLang === "ja") {
                    selectedSizeButton.textContent = "全て"
                } else {
                    selectedSizeButton.textContent = "All"
                }


                if (isSignedin) {
                    ownedNesosSpace.style.display = "none";
                    headerdiv.style.display = "none";
                } else {
                    for (var i = 0; i < emptySpace.length; i++) {
                        emptySpace[i].style.display = "none";
                    }
                    if (jsLang === "ja") {
                        selectedSizeButton.textContent = "全て"
                    } else {
                        selectedSizeButton.textContent = "All"
                    }
                }

            }
        }

    }
}

if (isSignedin) {
    if (idolButtons.length > 0) {
        idolButtons[0].onclick = function () {
            idolNesoSpace.style.display = "none";
            idolNesoSpace.innerHTML = "";
            ownedNesosSpace.style.display = "flex";

            idolNameHeader.style.display = "none";
            currentIdol.innerHTML = "";
            currentIdolIcon.style.display = "none";

            divCounters.style.display = "flex";
            totalContainer.style.display = "block";
            headerdiv.style.display = "flex";
            idolSelector.style.display = "flex";

            setTimeout(createSizeFilter, 100);
            generateOwnedNesos();
        }
    }
}


function generateOwnedNesos() {
    $.ajax({
        url: '/src/getOwnedNesos.php',
        type: 'POST',
        data: function () {
            var data = new FormData();

            data.append('UserName', username);
            data.append('varLang', jsLang);
            return data;
        }(),
        success: function (data) {
            onNesosLoaded(data, true);
        },
        error: function (data) {
            console.log("An error occurred when attempting to load Nesos: " + data);
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function onIdolsLoaded(data) {
    var barControls = idolSelectors.getElementsByClassName("collapsible");
    if (isSignedin) {
        while (barControls[1]) {
            idolSelectors.removeChild(barControls[1]);
        }
    } else {
        while (barControls[0]) {
            idolSelectors.removeChild(barControls[0]);
        }
    }

    const idolsList = JSON.parse(data);

    for (idolIndex in idolsList) {
        var idol = idolsList[idolIndex];

        var button = document.createElement("button");
        button.className = "collapsible";
        button.style.backgroundImage = "url('/src/img/" + idol["Name"].toLowerCase() + "/portrait.png')";
        button.style.backgroundColor = idol["Color"];
        button.id = "idol_" + idol["Name"].toLowerCase();
        button.setAttribute("full_name", idol["FullName"]);

        idolSelectors.appendChild(button);
    }
    hookIdolSelectors();
}

function onNesosLoaded(data, isownedList) {
    const nesosList = JSON.parse(data);
    if (isownedList) {
        ownedNesosSpace.innerHTML = "";
    } else {
        idolNesoSpace.innerHTML = "";
        currentIdol.innerHTML = nesosList[0]["DisplayName"];
    }


    for (nesoIndex in nesosList) {
        var neso = nesosList[nesoIndex];
        var nesoidol = neso.IdolName;
        var nesoidol = nesoidol.toLowerCase();

        var itemContainer = renderNesoContainer(neso, nesoidol);
        itemContainer.setAttribute("neso-id", neso["Id"]);
        itemContainer.setAttribute("idolname", nesoidol);
        itemContainer.setAttribute("class", "itemcontainer " + neso["Size"]);
        itemContainer.setAttribute("size", neso["Size"]);
        itemContainer.setAttribute("filtername", neso["DisplayName"])


        if (isSignedin) {
            itemContainer.onclick = function (event) {
                var clickedElement = event.target;
                while (clickedElement != null && clickedElement != this) {
                    if (clickedElement.classList.contains("bottomheader") || clickedElement.classList.contains("bottominfo")) {
                        return;
                    }
                    clickedElement = clickedElement.parentElement;
                }
                var index = this.getAttribute("neso-id");
                var cb = document.getElementById("neso_" + index);
                cb.checked = !cb.checked;
                idolCardClicked(this);
            }
        }

        if (isownedList) {
            var toph = itemContainer.getElementsByClassName("toph");
            var ownedby = itemContainer.getElementsByClassName("ownedby");

            if (neso["OwnedBy"] === 0) {
                if (jsLang === "ja") {
                    ownedby[0].innerHTML = "あなただけ！";
                } else {
                    ownedby[0].innerHTML = "Only you!";
                }
            } else if (neso["OwnedBy"] === 1) {
                if (jsLang === "ja") {
                    ownedby[0].innerHTML = "あなたと他のユーザー";
                } else {
                    ownedby[0].innerHTML = "You and another user";
                }
            } else {
                if (jsLang === "ja") {
                    ownedby[0].innerHTML = "あなたと他の " + neso["OwnedBy"] + " 人のユーザー";
                } else {
                    ownedby[0].innerHTML = "You and " + neso["OwnedBy"] + " other users";
                }
            }

            toph[0].className = "owned";
            ownedNesoSpace.appendChild(itemContainer);
        } else {
            idolNesoSpace.appendChild(itemContainer);
        }
    }
    generateCounters();
    generateTotal();
    createIdolFilter();
    dropdowndiv.style.display = 'grid';
    createSizeFilter();
}

if (isSignedin) {
    function idolCardClicked(sourceContainer) {
        var nesoId = sourceContainer.getAttribute("neso-id");
        var idolName = sourceContainer.getAttribute("idolname");
        var sourceRectangle = sourceContainer.getElementsByTagName("div")[0];

        var ownedNesoContainers = ownedNesosSpace.getElementsByClassName("itemcontainer");
        var container = null;
        var found = false;
        for (var i = 0; i < ownedNesoContainers.length; i++) {
            container = ownedNesoContainers[i];
            if (container.getAttribute("ownedneso-id") == nesoId) {
                found = true;
                break;
            }
        }

        if (found) {
            container.remove();
            generateCounters();
            generateTotal();
            createIdolFilter();
            createSizeFilter();

        } else {
            var sizeToCopy = sourceRectangle.getElementsByTagName("div")[0].getElementsByClassName("bhtext")[0].innerHTML;
            var imageToCopy = sourceRectangle.getElementsByTagName("div")[1].getElementsByClassName("nesoimg")[0].src;
            var imageToCopy = new URL(imageToCopy);
            var imageToCopy = imageToCopy.pathname;
            var imageToCopy = imageToCopy.split("/");
            var imageToCopy = imageToCopy[imageToCopy.length - 1];

            var nameToCopy = sourceRectangle.getElementsByTagName("div")[2].getElementsByClassName("bhtext")[0].innerHTML;
            var yearToCopy = sourceRectangle.getElementsByTagName("div")[3].getElementsByClassName("year")[0].innerHTML;
            var sizecmToCopy = sourceRectangle.getElementsByTagName("div")[3].getElementsByClassName("sizecm")[0].innerHTML;

            var neso = {
                Id: nesoId,
                Name: nameToCopy,
                Size: sizeToCopy,
                ImageFileName: imageToCopy,
                ReleaseYear: yearToCopy,
                ActualSize: sizecmToCopy,
                nesoidol: idolName
            }

            var copyContainer = renderNesoContainer(neso, '');

            ownedNesosSpace.appendChild(copyContainer);
        }

        setTimeout(createSizeFilter, 100);
        dropdowndiv.style.display = 'grid';

        $.ajax({
            url: 'src/save.php',
            type: 'POST',
            data: function () {
                var data = new FormData();
                data.append('NesoID', nesoId);
                return data;
            }(),
            success: function (data) {
                console.log("Neso " + nesoId + " saved! Response: " + data);
            },
            error: function (data) {
                console.log("An error occurred when calling Save: " + data);
            },
            cache: false,
            contentType: false,
            processData: false
        });

        var topheader = sourceRectangle.getElementsByTagName("div")[0];
        if (found) {
            topheader.className = "toph";
        } else {
            topheader.className = "owned";
        }
    }
}


function createSizeFilter() {
    var sizeFilterOptions = document.getElementById('sizeFilterOptions');
    sizeFilterOptions.innerHTML = '';

    var sizes = getSizesFromItems();
    sizes.forEach(function (size) {
        var option = createFilterOption(size);
        option.addEventListener('click', function (event) {
            event.preventDefault();
            updateSelectedSize(size);
            filterRefresh();
        });
        sizeFilterOptions.appendChild(option);
    });
}

function createIdolFilter() {
    var idolFilterOptions = document.getElementById('idolFilterOptions');
    idolFilterOptions.innerHTML = '';

    var idols = getIdolsFromItems();
    idols.forEach(function (idol) {
        var option = createFilterOption(idol);
        option.addEventListener('click', function (event) {
            event.preventDefault();
            updateSelectedIdol(idol);
            filterRefresh();
        });
        idolFilterOptions.appendChild(option);
    });
}

function createFilterOption(value) {
    var option = document.createElement('a');
    option.href = '#';
    option.textContent = value;
    return option;
}

function updateSelectedSize(size) {
    var selectedSizeButton = document.getElementById('selectedSize');
    selectedSizeButton.textContent = size.charAt(0).toUpperCase() + size.slice(1);
}

function updateSelectedIdol(idol) {
    var selectedIdolButton = document.getElementById('selectedIdol');
    selectedIdolButton.textContent = idol;
}


function filterRefresh() {
    var selectedIdol = document.getElementById("selectedIdol").innerText.toLowerCase();
    var selectedSize = document.getElementById("selectedSize").innerText.toLowerCase();
    var items = document.querySelectorAll('.itemcontainer');

    items.forEach(function (item) {
        var itemIdol = item.getAttribute('filtername').toLowerCase();
        var itemSize = item.getAttribute('size').toLowerCase();
        if ((selectedIdol === 'all' || selectedIdol === '全て' || itemIdol === selectedIdol) && (selectedSize === 'all' || selectedSize === '全て' || itemSize === selectedSize)) {
            item.style.display = 'inline-block';
        } else {
            item.style.display = 'none';
        }
    });
}

function getSizesFromItems() {
    const sizes = new Set();
    var items = "";
    let element = document.getElementById("ownedNesoSpace");
    let style = getComputedStyle(element);

    if (style.display === "flex") {
        items = ownedNesosSpace.querySelectorAll('.itemcontainer');
    } else {
        items = idolNesoSpace.querySelectorAll('.itemcontainer');
    }

    if (jsLang === "ja") {
        sizes.add("全て");
    } else {
        sizes.add("All");
    }
    for (let item of items) {
        const size = item.getAttribute('size');
        sizes.add(size);
    }
    return Array.from(sizes);
}

function getIdolsFromItems() {
    const idols = new Set();
    const items = document.querySelectorAll('.itemcontainer');
    if (jsLang === "ja") {
        idols.add("全て");
    } else {
        idols.add("All");
    }

    items.forEach((item) => {
        const idol = item.getAttribute('filtername');
        const words = idol.split(" ");
        if (idol.includes(" ")) {
            for (let i = 0; i < words.length; i++) {
                words[i] = words[i][0].toUpperCase() + words[i].substr(1);
            }
            idols.add(words.join(" "));
        } else {
            idols.add(idol.toUpperCase());

        }
    });
    return Array.from(idols);
}

function renderNesoContainer(neso, nesoidol) {
    var isOwned = false;
    if (isSignedin) {
        var ownedNesoContainers = ownedNesosSpace.getElementsByClassName("itemcontainer");
        for (var i = 0; i < ownedNesoContainers.length; i++) {
            container = ownedNesoContainers[i];
            if (container.getAttribute("ownedneso-id") == neso["Id"]) {
                isOwned = true;
                break;
            }
        }
    }

    var itemContainer = document.createElement("div");
    itemContainer.setAttribute("ownedneso-id", neso["Id"]);
    itemContainer.setAttribute("idolname", neso["nesoidol"]);
    itemContainer.setAttribute("class", "itemcontainer " + neso["Size"]);
    itemContainer.setAttribute("size", neso["Size"]);
    itemContainer.setAttribute("filtername", neso["DisplayName"])

    var rectangle = document.createElement("div");
    rectangle.className = "rectangle";

    var toph = document.createElement("div");
    toph.id = "toph_" + neso["Id"];
    toph.className = "toph";
    if (isSignedin) {
        if (isOwned) {
            toph.className = "owned";
        } else {
            toph.className = "toph";
        }
    }

    var size = document.createElement("p");
    size.className = "bhtext";
    size.style = "font-size: 25px;";
    size.innerHTML = neso["Size"];

    if (isSignedin) {
        var cb = document.createElement("input");
        cb.id = "neso_" + neso["Id"];
        cb.type = "checkbox";
        cb.style = "display: none;";
        cb.checked = isOwned;

        toph.appendChild(cb);
    }

    toph.appendChild(size);
    rectangle.appendChild(toph);

    var imageDiv = document.createElement("div");
    imageDiv.class = "image";

    var image = document.createElement("img");
    image.className = "nesoimg";
    image.src = "/src/img/" + (neso["nesoidol"] || nesoidol).replace(" ", "") + '/' + neso["ImageFileName"];

    imageDiv.appendChild(image);
    rectangle.appendChild(imageDiv);

    var bottomheader = document.createElement("div");
    bottomheader.className = "bottomheader";
    bottomheader.style.cursor = "pointer";
    bottomheader.style.borderRadius = "0 0 7px 7px";
    bottomheader.setAttribute("neso-id", neso["Id"]);

    var bottominfo = document.createElement("div");
    bottominfo.className = "bottominfo";
    bottominfo.style.display = "none";

    var nesoname = document.createElement("p");
    nesoname.className = "bhtext";
    nesoname.style = "text-align:center;";
    nesoname.innerHTML = neso["Name"];

    var arrowright = document.createElement("img");
    arrowright.className = "arrow rotate";
    arrowright.src = "/img/expand.png";
    arrowright.style.transform = "rotate(0deg)";

    var arrowleft = document.createElement("img");
    arrowleft.className = "arrow";
    arrowleft.src = "expand.png";
    arrowleft.style.visibility = "hidden";

    var releaseyear = document.createElement("p");
    releaseyear.style = "text-align:left;"

    var releaseyear1span = document.createElement("span");
    releaseyear1span.style.fontWeight = "bold";
    if (jsLang === "ja") {
        releaseyear1span.innerHTML = "発売年:&nbsp;"
    } else {
        releaseyear1span.innerHTML = "Release Year:&nbsp;"
    }

    var releaseyear2span = document.createElement("span");
    releaseyear2span.innerHTML = neso["ReleaseYear"];
    releaseyear2span.className = "year";

    releaseyear.appendChild(releaseyear1span);
    releaseyear.appendChild(releaseyear2span);

    var actualsize = document.createElement("p");
    actualsize.style = "text-align:left;"

    var actualsize1span = document.createElement("span");
    actualsize1span.style.fontWeight = "bold";
    if (jsLang === "ja") {
        actualsize1span.innerHTML = "実寸:&nbsp;"
    } else {
        actualsize1span.innerHTML = "Actual size:&nbsp;"
    }

    var actualsize2span = document.createElement("span");
    actualsize2span.innerHTML = neso["ActualSize"];
    actualsize2span.className = "sizecm";

    actualsize.appendChild(actualsize1span);
    actualsize.appendChild(actualsize2span);

    var ownedBy = document.createElement("p");
    ownedBy.style = "text-align:left;"
    var ownedBy1Span = document.createElement("span");
    ownedBy1Span.style.fontWeight = "bold";
    if (jsLang === "ja") {
        ownedBy1Span.innerHTML = "が所有している:&nbsp;"
    } else {
        ownedBy1Span.innerHTML = "Owned by:&nbsp;"
    }

    var ownedBy2Span = document.createElement("span");
    ownedBy2Span.className = "ownedby";

    if (isOwned) {
        if (neso["OwnedBy"] === 0) {
            if (jsLang === "ja") {
                ownedBy2Span.innerHTML = "あなただけ！";
            } else {
                ownedBy2Span.innerHTML = "Only you!";
            }
        } else if (neso["OwnedBy"] === 1) {
            if (jsLang === "ja") {
                ownedBy2Span.innerHTML = "あなたと他のユーザー";
            } else {
                ownedBy2Span.innerHTML = "You and another user";
            }
        } else {
            if (jsLang === "ja") {
                ownedBy2Span.innerHTML = "あなたと他の " + neso["OwnedBy"] + " 人のユーザー";
            } else {
                ownedBy2Span.innerHTML = "You and " + neso["OwnedBy"] + " other users";
            }
        }

    } else {
        if (jsLang === "ja") {
            ownedBy2Span.innerHTML = neso["OwnedBy"] + " ユーザー";
        } else {
            ownedBy2Span.innerHTML = neso["OwnedBy"] + " users";
        }
    }

    ownedBy.appendChild(ownedBy1Span);
    ownedBy.appendChild(ownedBy2Span);

    bottominfo.appendChild(releaseyear);
    bottominfo.appendChild(actualsize);
    bottominfo.appendChild(ownedBy);

    rectangle.appendChild(bottomheader);
    rectangle.appendChild(bottominfo);

    bottomheader.appendChild(arrowleft);
    bottomheader.appendChild(nesoname);
    bottomheader.appendChild(arrowright);

    itemContainer.appendChild(rectangle);

    bottomheader.addEventListener("click", function (event) {
        handleItemClick(event);
    });

    return itemContainer;
}

function generateTotal() {
    var ownedNesoContainers = ownedNesosSpace.getElementsByClassName("itemcontainer");
    totalContainer.innerHTML = "";

    var totaltext = document.createElement("p");
    totaltext.style = "display: inline-block;margin:0;";
    if (jsLang === "ja") {
        totaltext.innerHTML = '寝そべり保有総数:&nbsp;';
    } else {
        totaltext.innerHTML = 'Total:&nbsp;';
    }

    var total = document.createElement("p");
    total.style.display = ("inline-block");
    total.style.margin = 0;
    total.innerHTML = ownedNesoContainers.length;
    totalContainer.appendChild(totaltext);
    totalContainer.appendChild(total);
}

function generateCounters() {
    divCounters.innerHTML = "";
    var idolCounters = {};
    var ownedNesoContainers = ownedNesosSpace.getElementsByClassName("itemcontainer");

    for (var i = 0; i < ownedNesoContainers.length; i++) {
        var thisIdol = ownedNesoContainers[i].getAttribute("idolname").toLowerCase();
        if (idolCounters[thisIdol] === undefined) {
            idolCounters[thisIdol] = 1;
        } else {
            idolCounters[thisIdol] = idolCounters[thisIdol] + 1;
        }
    }

    var sortedCounters = Object.keys(idolCounters).map(function (key) {
        return [key, idolCounters[key]];
    });

    sortedCounters.sort(function (first, second) {
        return second[1] - first[1];
    });

    for (var key in sortedCounters) {
        var kvp = sortedCounters[key];
        var name = kvp[0];
        var count = kvp[1];
        var icon = document.createElement("img");
        icon.src = "/src/img/" + name.replace(" ", "") + '/icon.png';
        icon.className = "iconimg"

        var label = document.createElement("p");
        label.innerHTML = count;
        label.className = "textcounter"

        divCounters.appendChild(icon);
        divCounters.appendChild(label);
    }

    var toplogo = divCounters.getElementsByTagName("img")[0];
    var previousTop = document.getElementById("logoheader");
    if (previousTop != null) {
        previousTop.remove();
    }

    if (toplogo !== undefined) {
        var logoimg = document.createElement("img");
        logoimg.src = toplogo.src;
        logoimg.className = "iconimg";
        logoimg.id = "logoheader";

        if (headerdiv.children.length === 2) {
            headerdiv.insertBefore(logoimg, headerdiv.lastChild);
        } else {
            headerdiv.appendChild(logoimg);
        }

    }

}

function handleItemClick(event) {
    var clickedItem = event.currentTarget;
    var currentlySelected = clickedItem.parentNode;
    var currentBottominfo = currentlySelected.querySelector(".rectangle .bottominfo");
    var arrowdiv = currentlySelected.querySelector('.rectangle .bottomheader .rotate');
    var bottomheader = currentlySelected.querySelector(".rectangle .bottomheader");

    if (currentBottominfo.style.display === "none" || currentBottominfo.style.display === "") {
        currentBottominfo.style.display = "block";
        bottomheader.style.borderRadius = "0";
    } else {
        currentBottominfo.style.display = "none";
        bottomheader.style.borderRadius = "0 0 7px 7px";
    }

    if (arrowdiv.style.transform === "rotate(0deg)") {
        arrowdiv.style.transform = "rotate(180deg)";
    } else {
        arrowdiv.style.transform = "rotate(0deg)";
    }
    event.stopPropagation();
}


loadIdolSelectors();
