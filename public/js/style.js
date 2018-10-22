
$(function(){
    // 投诉建议
    // $('#submit').click(function(event){
    //     sugesstion();
    // })
    //我的预约,个人用户
    $('#person_btn').click(function(event){
        person();
    })
    //我的预约,企业用户
    $('#bussiness_btn').click(function(event){
        bunssiness();
    })
    
})
// 投诉建议
// function sugesstion(){
//     var sugesstion=$('#sugesstion').val();
//     if(sugesstion==""){
//         return alert('您还没有输入建议内容!')
//     }
//         console.log(sugesstion);
// }

//我的预约,个人用户
function person(){
    var name=$('#name').val();
    var number=$('#number').val();
    if(name==""||number==""){
        return alert('您还输入的内容不完整!');
    }
    console.log(name);
    console.log(number);
}
//我的预约,企业用户
function bunssiness(){
    var number=$('#bunssiness_number').val();
    if(number==""){
        return alert('您还没有输入企业注册号!');
    }
    console.log(number);
}