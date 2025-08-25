<article class="profile" id="layout"></article>
<script>
    (async function () {
        await builder.Storage._ensureReady?.();
        $(document).ready(function(){

            // Ajax Request
            $.ajax({
                url: '/api/profile/fetch',
                type: 'GET',dataType: 'json',
                success: async function(response) {

                    // Configure Storage
                    builder.Storage.setKey('profile');
                    await builder.Storage.set(response);
                    console.log(await builder.Storage.get());

                    // Retrieve the record
                    let record = await builder.Storage.get('record');

                    // Set the element
                    var element = $('#layout');

                    // Setup the layout
                    element.row = $(document.createElement('div')).appendTo(element);
                    element.details = $(document.createElement('div')).addClass('profile-details').appendTo(element.row);
                    element.col2 = $(document.createElement('div')).appendTo(element.row);

                    // Create User Block
                    element.details.avatar = $(document.createElement('img')).attr({
                        'class': 'avatar cursor-pointer',
                        'alt': 'Avatar',
                        'src': '/avatar?username=' + record.username
                    }).appendTo(element.details);
                    element.details.avatar.click(function(){
                        builder.Widget('vcard',{mode:'upload',data: record.vcard.id});
                    });
                    element.details.meta = $(document.createElement('div')).addClass('meta').appendTo(element.details);
                    element.details.meta.username = $(document.createElement('button')).attr({
                        'class': 'username btn btn-link text-decoration-none',
                        'type': 'button'
                    }).text(record.vcard.name ?? record.username).appendTo(element.details.meta);
                    element.details.meta.username.click(function(){
                        builder.Widget('vcard',{data: record.vcard.id});
                    });
                    element.details.meta.organization = $(document.createElement('button')).attr({
                        'class': 'organization btn btn-link text-decoration-none',
                        'type': 'button'
                    }).text(record.organization.vcard.name).appendTo(element.details.meta);
                    element.details.meta.organization.click(function(){
                        builder.Widget('vcard',{data: record.organization.vcard.id});
                    });
                    element.details.meta.metadata = $(document.createElement('div')).addClass('metadata').appendTo(element.details.meta);
                    element.details.meta.metadata.icon = $(document.createElement('i')).addClass('bi bi-clock me-1').appendTo(element.details.meta.metadata);
                    element.details.meta.metadata.timeago = $(document.createElement('time')).attr({
                        'class': 'timeago',
                        'datetime': record.created ?? new Date().toISOString(),
                    }).appendTo(element.details.meta.metadata);
                    const created = new Date(record.created ?? new Date().toISOString());
                    element.details.meta.metadata.timeago.attr({
                        'title': created.toLocaleString(),
                        'data-bs-toggle': 'tooltip',
                        'data-bs-title': created.toLocaleString(),
                    });
                    new bootstrap.Tooltip(element.details.meta.metadata.timeago);
                    element.details.meta.metadata.timeago.timeago();

                    // Create a Tabs component
                    const Tabs = builder.Component(
                        "tabs",
                        element.col2,
                        {
                            class: {
                                navbar: 'nav-pills',
                            },
                        },
                        async function(tabs,card){

                            // Retrieve the record
                            let record = await builder.Storage.get('record');

                            // Set the table
                            let table = 'users'

                            // Styling
                            card.tabs = {};
                            card._component.body.removeClass('card-body');

                            // Notes
                            <?php if($this->Helper->Core->isInstalled('notes')): ?>

                                // Retrieve the notes
                                let notes = await builder.Storage.get('dependencies:notes');

                                // Add the Notes tab
                                tabs.add(
                                    'notes',
                                    {
                                        icon: "stickies",
                                        label: builder.Locale.get("Notes"),
                                    },
                                    function(tab,nav){
                                        card.tabs.notes = tab;
                                        builder.Widget('notes',tab,{data: notes ?? {},targetTable: table,targetId: record.id})
                                    },
                                );
                            <?php endif; ?>

                            // Contacts
                            <?php if($this->Helper->Core->isInstalled('contacts')): ?>

                                // Retrieve the contacts
                                let contacts = await builder.Storage.get('dependencies:contacts');

                                // Add the Contacts tab
                                tabs.add(
                                    'contacts',
                                    {
                                        icon: "person-vcard",
                                        label: builder.Locale.get("Contacts"),
                                    },
                                    function(tab,nav){
                                        card.tabs.contacts = tab;
                                        builder.Widget("contacts",tab,{data: contacts ?? {},targetTable: table,targetId: record.id, default: record.vcard});
                                    },
                                );
                            <?php endif; ?>

                            // Files
                            <?php if($this->Helper->Core->isInstalled('files')): ?>

                                // Retrieve the files
                                let files = await builder.Storage.get('dependencies:files');

                                // Add the Files tab
                                tabs.add(
                                    'files',
                                    {
                                        icon: "file-earmark",
                                        label: builder.Locale.get("Files"),
                                    },
                                    function(tab,nav){
                                        card.tabs.files = tab;
                                        builder.Widget("files",tab,{data: files ?? {},targetTable: table,targetId: record.id,isPublic: 1});
                                    },
                                );
                            <?php endif; ?>

                            // Event
                            <?php if($this->Helper->Core->isInstalled('event')): ?>

                                // Retrieve the event
                                let event = await builder.Storage.get('dependencies:event');

                                // Add the Event tab
                                tabs.add(
                                    'event',
                                    {
                                        icon: "activity",
                                        label: builder.Locale.get("Activity"),
                                    },
                                    function(tab,nav){
                                        card.tabs.event = tab;
                                        builder.Widget("events",tab,{data: event ?? {},targetTable: table,targetId: record.id});
                                    },
                                );
                            <?php endif; ?>
                        },
                    );
                }
            });
        });
    })();
</script>
