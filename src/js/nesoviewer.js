var idolNesoSpace = document.getElementById('idolNesoSpace');
var idolButtons = document.getElementsByClassName('collapsible');
var currentIdol = document.getElementById('currentIdol');
var currentIdolIcon = document.getElementById('currentIdolIcon');
var idolNameHeader = document.getElementById('idolnameheader');
var sizes = document.getElementById('sizes');
var filterContainer = document.getElementById('filterContainer');
var emptySpace = document.getElementById('startmsg')


for(var i = 0; i < idolButtons.length; i++) {
    if(idolButtons[i].id.startsWith("idol_")) {
        idolButtons[i].onclick = function() {
            emptySpace.style.display = "none";
            var idolName = this.id.substring(5);
            var fullName = this.getAttribute("full_name");
            idolNesoSpace.style.display = "flex";
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

        var itemContainer = document.createElement("div");
        itemContainer.setAttribute("neso-id", neso["Id"]);
        itemContainer.setAttribute("idolname", idolName);
        itemContainer.setAttribute("class", "itemcontainer filterDiv show " + neso["Size"]);
        itemContainer.setAttribute("size", neso["Size"])

        var rectangle = document.createElement("div");
        rectangle.className = "rectangle";

        var toph = document.createElement("div");
        toph.id = "toph_" + neso["Id"];
        toph.className = "toph";

        var size = document.createElement("p");
        size.className = "bhtext";
        size.style = "font-size: 25px;";
        size.innerHTML = neso["Size"];

        toph.appendChild(size);
        rectangle.appendChild(toph);

        var imageDiv = document.createElement("div");
        imageDiv.class = "image";

        var image = document.createElement("img");
        image.className = "nesoimg";
        image.src = "/src/img/" + idolName + '/' + neso["ImageFileName"];

        imageDiv.appendChild(image);
        rectangle.appendChild(imageDiv);
        
        var bottom = document.createElement("div");
        bottom.className = "bottom";

        var bottomheader = document.createElement("div");
        bottomheader.className = "bottomheader";

        var bottominfo = document.createElement("div");
        bottominfo.className = "bottominfo";

        var nesoname = document.createElement("p");
        nesoname.className = "bhtext";
        nesoname.style = "text-align:left;";
        nesoname.innerHTML = neso["Name"];

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

        actualsize.appendChild(actualsize1span);
        actualsize.appendChild(actualsize2span);

        var exclusive = document.createElement("p");
        exclusive.style = "text-align:left;"

        var exclusive1span = document.createElement("span");
        exclusive1span.style.fontWeight = "bold";
        if (jsLang === "ja") {
            exclusive1span.innerHTML = "会員限定？:&nbsp;"
        } else {
            exclusive1span.innerHTML = "Exclusive to member?:&nbsp;"
        }

        var exclusive2span = document.createElement("span");
        if (jsLang === "ja") {
            actualsize1span.innerHTML = "実寸:&nbsp;"
        } else {
            actualsize1span.innerHTML = "Actual size:&nbsp;"
        }
        exclusive2span.innerHTML = neso["Exclusive"];

        exclusive.appendChild(exclusive1span);
        exclusive.appendChild(exclusive2span);

        bottominfo.appendChild(releaseyear);
        bottominfo.appendChild(actualsize);
        bottominfo.appendChild(exclusive);

        bottom.appendChild(bottomheader);
        bottom.appendChild(bottominfo);

        bottomheader.appendChild(nesoname);
        rectangle.appendChild(bottom);                                          
        
        itemContainer.appendChild(rectangle);
        idolNesoSpace.appendChild(itemContainer);
    }
    refreshFilterButtonsIdol()
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

function refreshFilterButtonsIdol() {
    const filterContainer = document.getElementById('filterContainer');
    filterContainer.innerHTML = '';
  
    createFilterButtonsIdol();
  }

