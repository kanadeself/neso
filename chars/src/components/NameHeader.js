function RenderNameHeader(name, char) {
    const Nameheader = () => {
        return (
            <div className="header">
                <div className="headertext">
                    <h1>{name}</h1>
                    <img className="icon" src={`img/${char}/icon.png`} />
                </div>
                <div className="boton"><a href="./" className="butt"><p className="t1">Back to characters</p></a></div>
            </div>
        )
    }

    ReactDOM.render(<Nameheader />, document.getElementById('header'))
}