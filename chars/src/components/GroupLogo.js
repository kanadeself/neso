
function GroupLogo(name){
    return (
        <div className="logos">
            <img className={"logo-image " + name} src={`img/${name}.png`} />
        </div>
    )
}