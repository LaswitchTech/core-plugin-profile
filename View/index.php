<article id="layout"></article>
<script>
    (function () {
        $(document).ready(function(){
            builder.Layout('user',"#layout",{endpoint: '/profile/fetch',disable: ['documents']});
        });
    })();
</script>
