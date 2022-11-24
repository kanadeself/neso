async function fetchData(url, cb){
    fetch(url)
    .then(response => response.json())
    .then(result => cb(result));
}