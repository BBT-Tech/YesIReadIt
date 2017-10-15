<?php include "header.php"?>
<div id="container" class="container">
    <div class="row" v-for="(group_info,group_id) in groups_info" style="margin-bottom: 0;">
        <div class="col s12 l6 offset-l3">
            <div class="card">
                <div class="card-content teal lighten-3 white-text">
                    <span class="card-title">{{ group_info.group_name }}:</span>
                    <ul class="collapsible popover teal-text" data-collapsible="accordion">
                        <li v-for="notice in notices_info[group_id]">
                            <div class="collapsible-header">{{ notice.notice_title }}</div>
                            <div class="collapsible-body white " style="padding-bottom: 0;">
                                <div v-html="notice.notice_content" class="markdown-body"></div>
                                <div class="row right-align">
                                    <button class="waves-effect waves-light btn" @click="answer_notice(notice)">Read It!</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 0;">
        <div class="col s12 l6 offset-l3">
            <div class="card">
                <div class="card-content blue  white-text">
                    <span class="card-title">最近已读:</span>
                    <ul class="collapsible popover teal-text" data-collapsible="accordion">
                        <li v-for="notice in read_notices_info">
                            <div class="collapsible-header">{{ notice.notice_title }}</div>
                            <div class="collapsible-body white" style="padding-bottom: 0;">
                                <div class="no-big-text" v-html="notice.notice_content"></div>
                                <div class="row right-align">
                                    <a class="waves-effect waves-light btn red" :href="notice.notice_id | noticeView">详情</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="answer_modal" class="modal">
        <div class="modal-content" style="padding-bottom: 0">
            <form class="col s12">
                <div class="row" style="margin-bottom: 0">
                    <div class="input-field col s12">
                        <textarea id="answer_area" class="materialize-textarea ">收到</textarea>
                        <label for="answer_area">回复内容</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class=" modal-action modal-close waves-effect waves-green btn-flat" @click="setNoticeRead">提交</button>
        </div>
    </div>
</div>

<script>
    var basic_url="<?php echo _HTTP;?>";
    var vm=new Vue(
        {
            el: "#container",
            data: {
                groups_info:[],
                notices_info:[],
                read_notices_info:[],
                cur_notice_info:{}
            },
            filters: {
                noticeView:function(id)
                {
                    return basic_url + "notice/read/id/"+id;
                },
                html2string:function(val)
                {
                    var div=document.createElement("div");
                    div.innerHTML=val;
                    return div.innerText;
                }
            },
            created: function(){
                axios.get(basic_url + 'userAPI/getUnreadNotices/')
                    .then(function(response)
                    {
                        if(response.data.status==0){
                            var groups=response.data.groups;
                            var notices=response.data.notices;
                            for(var index in groups)
                            {
                                var item=groups[index];
                                vm.notices_info[item.group_id]=[];
                            }
                            for(var index in notices)
                            {
                                var item=notices[index];
                                vm.notices_info[item.group_id].splice(-1,0,item);
                            }

                            vm.groups_info=groups;
                            Vue.nextTick(function () {
                                $('.collapsible').collapsible();
                            });
                        }
                        else
                        {
                            Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                        }
                    });
                axios.get(basic_url + 'userAPI/getReadNotices/')
                    .then(function(response)
                    {
                        if(response.data.status==0){
                            var notices=response.data.notices;
                            vm.read_notices_info=notices;
                            Vue.nextTick(function () {
                                $('.collapsible').collapsible();
                            });
                        }
                        else
                        {
                            Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                        }
                    });
            },
            methods: {
                answer_notice:function(notice_info)
                {
                    vm.cur_notice_info=notice_info;
                    $('#answer_modal').modal('open');
                },
                setNoticeRead:function()
                {
                    var obj={
                        notice_id:vm.cur_notice_info.notice_id,
                        answer:$('#answer_area').val()
                    };

                    axios.post(basic_url + 'userAPI/replyNotice/',JSON.stringify(obj))
                        .then(function(response)
                        {
                            if(response.data.status==0) {
                                Materialize.toast('<span class="">回复成功！正在跳转...</span>', 2000);
                                delayJump(basic_url + "notice/read/id/" + vm.cur_notice_info.notice_id,1000);
                            }

                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                            }
                        });
                }
            }
        }
    );
</script>
<?php include "footer.php"?>