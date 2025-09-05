<article id="layout"></article>
<script>
    (function () {
        $(document).ready(function(){
            builder.Layout('user',"#layout",{url: '/api/profile/fetch',disable: ['documents']});
        });
    })();
</script>
