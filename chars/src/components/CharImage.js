function CharImage(char){
    return (
        <div id={char.name} style={{padding: '8px'}}>
            <a href={`./?char=${char.name.toLowerCase()}`}>
                <img src={`./img/${char.name.toLowerCase()}/portrait.png`} className="round-image btni" style={
                    {
                        backgroundColor: char.color,
                        borderRadius: '50%', 
                        width:'10rem'}}/>
            </a>
        </div>
    )
}