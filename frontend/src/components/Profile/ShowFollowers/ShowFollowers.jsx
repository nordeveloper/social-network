import React, { useState, useEffect } from 'react';
import { connect } from 'react-redux';
import { Modal, List} from 'antd';
import './ShowFollowers.scss';
import { IMAGES_URL } from '../../../api-config';
import { getById } from '../../../redux/actions/users'

import Follow from '../Follow/Follow';
import Unfollow from '../Unfollow/Unfollow';

const ShowFollowers = props => {
    const [visible, setVisible] = useState();
    const showModal = () => { setVisible(true); };
    const hideModal = () => { setVisible(false); };

    useEffect(() => { 
        getById(props.currentUser?.id); 
    }, []);
    return (
        <div>
            <div className="data" onClick={showModal} style={props.currentUser.followers.length>0?{cursor:'pointer'}:{}}>
                <span className="bold">{props.currentUser?.followers.length}</span> seguidores
            </div>
            <Modal title="Seguidores" visible={visible} onOk={hideModal} onCancel={hideModal} footer={null}>
                <List header={null} footer={null} dataSource={[
                    props.currentUser.followers.map(follower => {
                        const isMe = props.myUser?.id === follower.id;
                        const isAlreadyFollowed = props.myUser.followings?.filter(f => f.id === follower.id).length>0 ? true : false;
                        return(
                        <div className="userFollowers" key={follower.id}>
                            <div className="imgName">
                                <img src={IMAGES_URL + follower.pic} alt="Foto de perfil" />
                                <div className="names">
                                    <span className="username">{follower.username}</span>
                                    <span>{follower.name}</span>
                                </div>
                            </div>
                            { !isMe && isAlreadyFollowed && <Follow myUser={props.myUser} currentUser={follower} />}
                            { !isMe && !isAlreadyFollowed && <Unfollow myUser={props.myUser} currentUser={follower} />}
                        </div>)
                    })
                ]} 
                    renderItem={item => (
                        <List.Item>                            
                            {item}
                        </List.Item>
                    )}
                />
            </Modal>
        </div>
    )
}

const mapStateToProps = ({user}) => ({ myUser: user.myUser, currentUser: user.currentUser });
export default connect(mapStateToProps)(ShowFollowers);