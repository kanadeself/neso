function RenderCharList(data) {
    const CharList = () => {
        return (
            <div className="character">
                <div class="nav-top">
                    <a href="../chars/" class="nav-text text-active">Database</a>
                    <a href="../guide.html" class="nav-text">Buying guide</a>
                </div>

                {data.groups.map((group) => {
                    return (
                        <>
                            {data[group] && GroupLogo(group)}
                            {data[group] && data[group].map((e) => CharImage(e))}
                        </>
                    )
                })}
            </div>
        )
    };

    ReactDOM.render(<CharList />, document.getElementById('root'))
}