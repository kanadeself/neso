function RenderNameHeader(name, char) {
    const Nameheader = () => {
        return (
            <div className="header">
                <div class="nav-top">
                    <a href="../chars/" class="nav-text">Database</a>
                    <a href="../guide" class="nav-text">Buying guide</a>
                </div>
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