function RenderMButtons(msizes) {
    const MButtons = () => {
        return (
            <>
                <button id="all-button" className="btn active" onClick={(e) => filterSelection('all')}>Show all</button>
                {msizes && msizes.map((msize) => {
                    return <button id={msize + "-button"} className="btn" onClick={(e) => filterSelection(msize)}>{msize}</button>
                })}
            </>
        )
    }

    ReactDOM.render(<MButtons />, document.getElementById('myBtnContainer'))
}

