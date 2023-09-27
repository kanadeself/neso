var idolNesoSpace = document.getElementById('idolNesoSpace');
var ownedNesosSpace = document.getElementById('ownedNesoSpace');
var idolButtons = document.getElementsByClassName('collapsible');
var currentIdol = document.getElementById('currentIdol');
var currentIdolIcon = document.getElementById('currentIdolIcon');
var idolNameHeader = document.getElementById('idolnameheader');
var divCounters = document.getElementById('divCounters');
var sizes = document.getElementById('sizes');
var filterContainer = document.getElementById('filterContainer');
var totalContainer = document.getElementById('totalCounter');


if(idolButtons.length > 0) {
    idolButtons[0].onclick = function() {
        idolNesoSpace.style.display = "none";
        ownedNesosSpace.style.display = "flex";
        idolNameHeader.style.display = "none";
        currentIdol.innerHTML = "";
        currentIdolIcon.style.display = "none";
        generateCounters();
        generateTotal();
        divCounters.style.display = "flex";
        totalContainer.style.display = "block";
        refreshFilterButtonsOwned();
    }

}


for(var i = 0; i < idolButtons.length; i++) {
    if(idolButtons[i].id.startsWith("idol_")) {
        idolButtons[i].onclick = function() {
            divCounters.style.display = "none";
            totalContainer.style.display = "none";
            var idolName = this.id.substring(5);
            var fullName = this.getAttribute("full_name");
            idolNesoSpace.style.display = "flex";
            ownedNesosSpace.style.display = "none";
            $.ajax({
                url: '/src/getNesos.php',
                type: 'POST',               
                data: function() {
                    var data = new FormData();
                    data.append('IdolName', idolName); 
                    data.append('varLang', jsLang);
                    return data;
                }(),
                success: function (data) {
                    onNesosLoaded(data, idolName);
                },
                error: function (data) {
                    console.log("An error occurred when attempting to load Nesos: " + data);
                },
                cache: false,
                contentType: false,
                processData: false
            });
            idolNameHeader.style.display = "flex";
            currentIdol.innerHTML = fullName;
            currentIdolIcon.style.display = "inline-block";
            currentIdolIcon.src = "/src/img/" + idolName + '/icon.png';
        }
    }
    refreshFilterButtonsIdol()

}

function onNesosLoaded(data, idolName) {
    const nesosList = JSON.parse(data);
    idolNesoSpace.innerHTML = "";
    for(nesoIndex in nesosList) {
        var neso = nesosList[nesoIndex];
        var isOwned = ownedNesos.includes(parseInt(neso["Id"]));

        var itemContainer = document.createElement("div");
        itemContainer.setAttribute("neso-id", neso["Id"]);
        itemContainer.setAttribute("idolname", idolName);
        itemContainer.setAttribute("class", "itemcontainer filterDiv show " + neso["Size"]);
        itemContainer.setAttribute("size", neso["Size"])
        itemContainer.onclick = function() {
            var index = this.getAttribute("neso-id");
            var cb = document.getElementById("neso_" + index);
            cb.checked = !cb.checked;
            idolCardClicked(this);
        }

        var rectangle = document.createElement("div");
        rectangle.className = "rectangle";

        var toph = document.createElement("div");
        toph.id = "toph_" + neso["Id"];
        if(isOwned) {
            toph.className = "owned";
        } else {
            toph.className = "toph";
        }

        var size = document.createElement("p");
        size.className = "bhtext";
        size.style = "font-size: 25px;";
        size.innerHTML = neso["Size"];

        var cb = document.createElement("input");
        cb.id = "neso_" + neso["Id"];
        cb.type = "checkbox";
        cb.style = "display: none;";
        cb.checked = isOwned;

        toph.appendChild(cb);
        toph.appendChild(size);
        rectangle.appendChild(toph);

        var imageDiv = document.createElement("div");
        imageDiv.class = "image";

        var image = document.createElement("img");
        image.className = "nesoimg";
        image.src = "/src/img/" + idolName + '/' + neso["ImageFileName"];

        imageDiv.appendChild(image);
        rectangle.appendChild(imageDiv);

        var bottomheader = document.createElement("div");
        bottomheader.className = "bottomheader";

        var nesoname = document.createElement("p");
        nesoname.className = "bhtext";
        nesoname.innerHTML = neso["Name"];

        bottomheader.appendChild(nesoname);
        rectangle.appendChild(bottomheader);                                          
        
        itemContainer.appendChild(rectangle);
        idolNesoSpace.appendChild(itemContainer);
    }
    refreshFilterButtonsIdol()
}

function idolCardClicked(sourceContainer) {
    var nesoId = sourceContainer.getAttribute("neso-id");
    var idolName = sourceContainer.getAttribute("idolname");
    var nesosize = sourceContainer.getAttribute("size")
    var sourceRectangle = sourceContainer.getElementsByTagName("div")[0];
    
    // Get all divs in the My Collection space with class itemcontainer
    var ownedNesoContainers = ownedNesosSpace.getElementsByClassName("itemcontainer");
    var container = null;
    var found = false;
    for(var i = 0; i < ownedNesoContainers.length; i++) {
        container = ownedNesoContainers[i];
        // Look for the container with the same nesoID as the container the user just clicked in the Idol space
        if(container.getAttribute("ownedneso-id") == nesoId) {
            found = true;
            break;
        }
    }
    

    // If it's found, we remove it - if it's not found, we add it.
    // We need to invert the neso's state

    if(found) {
        container.remove();
    } else {
        // Create a copy of the selected neso itemcontainer to render in the My Collection space
        // then add it

        var sizeToCopy = sourceRectangle.getElementsByTagName("div")[0].getElementsByClassName("bhtext")[0].innerHTML;
        var imageToCopy = sourceRectangle.getElementsByTagName("div")[1].getElementsByClassName("nesoimg")[0].src;
        var nameToCopy = sourceRectangle.getElementsByTagName("div")[2].getElementsByClassName("bhtext")[0].innerHTML;

        var copyContainer = document.createElement("div");   
        copyContainer.setAttribute("ownedneso-id", nesoId); 
        copyContainer.setAttribute("idolname", idolName);
        copyContainer.setAttribute("size", nesosize);
        copyContainer.setAttribute("class", "itemcontainer filterDiv show " + nesosize);

        var copyRectangle = document.createElement("div");
        copyRectangle.className = "rectangle";

        // First div - the top header for the size
        var copyToph = document.createElement("div");
        copyToph.id = "toph_" + nesoId;
        copyToph.className = "owned";

        // The neso size
        var copySize = document.createElement("p");
        copySize.className = "bhtext";
        copySize.style = "font-size: 25px;";
        copySize.innerHTML = sizeToCopy;

        // Add the Size text to the top header Div
        copyToph.appendChild(copySize);

        // Add the top header Div to the rectangle
        copyRectangle.appendChild(copyToph);

        // Second div - the image div
        var copyImageDiv = document.createElement("div");
        copyImageDiv.class = "image";

        // The neso image
        var copyImage = document.createElement("img");
        copyImage.className = "nesoimg";
        copyImage.src = imageToCopy;

        // Add the image to the image div
        copyImageDiv.appendChild(copyImage);

        // Add the image div to the rectangle
        copyRectangle.appendChild(copyImageDiv);

        // Third and last header - the bottom header for the name
        var copyBottomHeader = document.createElement("div");
        copyBottomHeader.className = "bottomheader";

        // The neso name
        var copyNesoName = document.createElement("p");
        copyNesoName.className = "bhtext";
        copyNesoName.innerHTML = nameToCopy;

        // Add the name to the bottom header div
        copyBottomHeader.appendChild(copyNesoName);

        // Add the bottom header div to rectangle
        copyRectangle.appendChild(copyBottomHeader);

        // Add the rectangle to the container
        copyContainer.appendChild(copyRectangle);

        // Add the container to the owned nesos space!
        ownedNesosSpace.appendChild(copyContainer);
        
    }

    // Call save.php to store the change in DB
    $.ajax({
        url: '/src/save.php',
        type: 'POST',               
        data: function(){
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
    if(found) {
        topheader.className = "toph";
        delete ownedNesos[ownedNesos.indexOf(parseInt(nesoId))];
    } else {
        topheader.className = "owned";
        ownedNesos.push(parseInt(nesoId));
    }
}

function generateTotal() {
    var ownedNesoContainers = ownedNesosSpace.getElementsByClassName("itemcontainer");
    totalContainer.innerHTML = "";
    var totaltext = document.createElement("p");
    totaltext.style = "display: inline-block;margin:0;";
    if (jsLang === "ja") {
        totaltext.innerHTML = '寝そべり保有総数：&nbsp;';
    } else {
        totaltext.innerHTML = 'Total nesos owned:&nbsp;';
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

    for(var i = 0; i < ownedNesoContainers.length; i++) {
        var currentIdol = ownedNesoContainers[i].getAttribute("idolname").toLowerCase();
        if(idolCounters[currentIdol] === undefined) {
            idolCounters[currentIdol] = 1;
        } else {
            idolCounters[currentIdol] = idolCounters[currentIdol] + 1;
        }
    }

    var sortedCounters = Object.keys(idolCounters).map(function(key) {
        return [key, idolCounters[key]];
      });
      
      sortedCounters.sort(function(first, second) {
        return second[1] - first[1];
      });


    for(var key in sortedCounters) {
        var kvp = sortedCounters[key];        
        var name = kvp[0];
        var count = kvp[1];
       var icon = document.createElement("img");
        icon.src = "/src/img/" + name + '/icon.png';
        icon.className = "iconimg"

        var label = document.createElement("p");
        label.innerHTML = count;
        label.className = "textcounter"

        divCounters.appendChild(icon);
        divCounters.appendChild(label);
    }
    var toplogo = divCounters.getElementsByTagName("img")[0];
    var headerdiv = document.getElementById("headerdiv");
    var previousTop = document.getElementById("logoheader");
    if (previousTop != null) {
        previousTop.remove();
    }
    var logoimg = document.createElement("img");
    logoimg.src = toplogo.src;
    logoimg.className = "iconimg";
    logoimg.id = "logoheader";
    headerdiv.appendChild(logoimg);
}

// THE FOLLOWING IS EXTREMELY SHIT

function filterBySize(size) {
  const items = document.querySelectorAll('.itemcontainer');
  items.forEach((item) => {
    const itemSize = item.getAttribute('size');
    if (itemSize === size || size === 'all') {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
}
  
  function getSizesfromIdols() {
    const sizes = new Set();
    const idolNesoSpace = document.getElementById('idolNesoSpace');
    const items = idolNesoSpace.querySelectorAll('.itemcontainer');
  
    items.forEach((item) => {
      const size = item.getAttribute('size');
      sizes.add(size);
    });
  
    return Array.from(sizes);
  }

  function getSizesfromOwned() {
    const sizes = new Set();
    const idolNesoSpace = document.getElementById('ownedNesoSpace');
    const items = idolNesoSpace.querySelectorAll('.itemcontainer');
  
    items.forEach((item) => {
      const size = item.getAttribute('size');
      sizes.add(size);
    });
  
    return Array.from(sizes);
  }
  
  function createFilterButtonsOwned() {
    const sizes = getSizesfromOwned();
    const filterContainer = document.getElementById('filterContainer');
    
    const showAllButton = document.createElement('button');
    if (jsLang === "ja") {
        showAllButton.textContent = 'すべて表示する';
    } else {
        showAllButton.textContent = 'Show All';
    }
    showAllButton.className = "btn active"
    showAllButton.addEventListener('click', () => {
        const buttons = filterContainer.querySelectorAll('button');
        buttons.forEach((btn) => {
          btn.classList.remove('active');
        });
    
        showAllButton.classList.add('active');
    
        filterBySize('all');
      });
      filterContainer.appendChild(showAllButton);
    
    

    sizes.forEach((size) => {
      const button = document.createElement('button');
      button.textContent = size;
      button.className = "btn"
      button.addEventListener('click', () => {
        const buttons = filterContainer.querySelectorAll('button');
        buttons.forEach((btn) => {
          btn.classList.remove('active');
        });
  
        button.classList.add('active');
  
        filterBySize(size);
      });
      filterContainer.appendChild(button);
    });

  }

  function createFilterButtonsIdol() {
    const sizes = getSizesfromIdols();
    const filterContainer = document.getElementById('filterContainer');
    
    const showAllButton = document.createElement('button');
    if (jsLang === "ja") {
        showAllButton.textContent = 'すべて表示する';
    } else {
        showAllButton.textContent = 'Show All';
    }    showAllButton.className = "btn active"
    showAllButton.addEventListener('click', () => {
        const buttons = filterContainer.querySelectorAll('button');
        buttons.forEach((btn) => {
          btn.classList.remove('active');
        });
    
        showAllButton.classList.add('active');
    
        filterBySize('all');
      });
      filterContainer.appendChild(showAllButton);
    

    sizes.forEach((size) => {
      const button = document.createElement('button');
      button.textContent = size;
      button.className = "btn"
      button.addEventListener('click', () => {
        const buttons = filterContainer.querySelectorAll('button');
        buttons.forEach((btn) => {
          btn.classList.remove('active');
        });
  
        button.classList.add('active');
  
        filterBySize(size);
      });
      filterContainer.appendChild(button);
    });

  }

function refreshFilterButtonsOwned() {
    const filterContainer = document.getElementById('filterContainer');
    filterContainer.innerHTML = '';
  
    createFilterButtonsOwned();
  }

function refreshFilterButtonsIdol() {
    const filterContainer = document.getElementById('filterContainer');
    filterContainer.innerHTML = '';
  
    createFilterButtonsIdol();
  }

