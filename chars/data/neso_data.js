import context from "./nesodata.json" assert { type: 'json' };

const source = document.querySelector("#list").innerHTML;
const template = Handlebars.compile(source);
const html = template(context);
const destination = document.querySelector(".neso");

destination.innerHTML = html; 