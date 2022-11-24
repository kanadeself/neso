function RenderNesoContainer(nesos, char) {
    const NesoContainer = () => {
        return (
            <div className="neso">
                {nesos && nesos.map((neso) => {
                    if (!neso.msize) return;
                    return (Neso(neso, char))
                })}
            </div>
        )
    };

    ReactDOM.render(<NesoContainer />, document.getElementById('root'))
}

