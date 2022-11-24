const queryParams = new URLSearchParams(window.location.search);
const char = queryParams.get('char');

fetchData('data/characters.json', (characters) => {
    if (!characters.characters.includes(char)) {
        fetchData('./data/chardata.json', (data) => {
            RenderCharList(data);
        })
    } else {
        fetchData('data/nesodata.json', (nesoData) => {

            const nesos = nesoData[char]
            console.log(nesos)

            let msizes = [];

            nesos.forEach((neso) => {
                if (!neso.msize) return;
                if (!msizes.includes(neso.msize))
                    msizes.push(neso.msize)
            })


            RenderNameHeader(nesoData[char][0], char)
            RenderMButtons(msizes)
            RenderNesoContainer(nesos, char)
        })
    }
})
