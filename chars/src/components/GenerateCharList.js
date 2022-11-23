function RenderCharList(data){
    const CharList = () => {
        return (
            <div className="character">
    
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