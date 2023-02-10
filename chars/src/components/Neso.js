function Neso(neso, char) {
    return (
        <div className={"itemcontainer filterDiv show " + neso.msize}>
            <div className="rectangle">
                <div className="toph">
                    <p className="bhtext" style={{ textAlign: 'center', fontSize: '25px' }}>{neso.msize}</p>
                </div>
                <div className="image">
                    <img src={`img/${char}/${neso.img}`} className="nesoimg" />
                </div>
                <div className="bottom">
                    <div className="bottomheader">
                        <p className="bhtext">{neso.name}</p>
                    </div>
                    <div className="bottominfo">
                        <p>
                            <span style={{ fontWeight: 'bold' }}>Release Year:&nbsp;</span>
                            <span>{neso.year}</span>
                        </p>
                        <p>
                            <span style={{ fontWeight: 'bold' }}>Actual Size:&nbsp;</span>
                            <span>{neso.size}</span>
                        </p>
                        <p>
                            <span style={{ fontWeight: 'bold' }}>Exclusive to member?:&nbsp;</span>
                            <span>{neso.exclusive}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>)
}