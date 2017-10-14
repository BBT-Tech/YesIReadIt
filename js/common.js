/**
 * Created by i on 2017/4/19.
 */

function delayJump(url,delay_time)
{
    setTimeout(function(){
        window.location=url;
    },delay_time);
}
function delayRefresh(delay_time)
{
    setTimeout(function(){
        window.location.reload();
    },delay_time);
}
