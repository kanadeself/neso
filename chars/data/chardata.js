import context from "./chardata.json" assert { type: 'json' };

const source = document.querySelector("#list").innerHTML;
const template = Handlebars.compile(source);

Handlebars.registerHelper('toLowerCase', function(str) {
    return str.toLowerCase();
  });

const html = template(context);
const destination = document.querySelector(".character");

destination.innerHTML = html; 