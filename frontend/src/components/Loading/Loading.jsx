import React from 'react';
import Icon from '../../../src/img/icon.png';
const Loading = () => {
    return (
        <div style={{height:'80vh', width:'100vw', display:'flex', alignItems:'center', justifyContent:'center'}}>
            <img src={Icon} alt="" style={{opacity:0.5, height:'10vh'}} />
        </div>
    )
}

export default Loading;