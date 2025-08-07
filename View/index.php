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
                            card._component.body.removeClass('card-body');

                            // Notes
                            <?php if($this->Helper->Core->isInstalled('notes')): ?>

                                // Retrieve the notes
                                let notes = await builder.Storage.get('dependencies:notes');

                                // Add the Notes tab
                                tabs.add(
                                    'notes-widget',
                                    {
                                        icon: "stickies",
                                        label: builder.Locale.get("Notes - Widget"),
                                    },
                                    function(tab,nav){
                                        card.notes = tab;
                                        // tab.addClass('card-body');
                                        builder.Widget(
                                            'notes',
                                            tab,
                                            {
                                                data: notes ?? {},
                                                targetTable: table,
                                                targetId: record.id,
                                            },
                                            function(widget, component){
                                                console.log(widget, component);
                                            },
                                        )
                                    },
                                );

                                // Add the Notes tab
                                tabs.add(
                                    'notes',
                                    {
                                        icon: "stickies",
                                        label: builder.Locale.get("Notes"),
                                    },
                                    function(tab,nav){
                                        card.notes = tab;
                                        NotesFeed(notes ?? [], tab, table, record.id);
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
                                        card.contacts = tab;
                                        ContactsFeed(contacts ?? [], tab, {
                                            "category": "Contact",
                                            "address": record.vcard.address,
                                            "city": record.vcard.city,
                                            "country": record.vcard.country.code,
                                            "state": record.vcard.state.code,
                                            "zipcode": record.vcard.zipcode,
                                            "locale": record.vcard.locale,
                                            "phone": record.vcard.phone,
                                            "targetTable": table,
                                            "targetId": record.id,
                                        });
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
                                        card.files = tab;
                                        FilesFeed(files ?? [], tab, {
                                            targetTable: table,
                                            targetId: record.id,
                                            isPublic: 1,
                                        });
                                    },
                                );
                            <?php endif; ?>

                            // Event
                            <?php if($this->Helper->Core->isInstalled('event')): ?>

                                // Retrieve the event
                                let event = await builder.Storage.get('dependencies:event');

                                // Add the Event tab
                                tabs.add(
                                    'activities',
                                    {
                                        icon: "activity",
                                        label: builder.Locale.get("Activity"),
                                    },
                                    function(tab,nav){
                                        tab.addClass('px-4 py-3');
                                        card.activities = tab;
                                        EventFeed(event ?? [], tab);
                                    },
                                );
                            <?php endif; ?>

                            // Relationship
                            <?php if($this->Helper->Core->isInstalled('relationship')): ?>

                                // Retrieve the relationship
                                let relationship = await builder.Storage.get('dependencies:relationship');

                                // Add the Relationship tab
                                tabs.add(
                                    'related',
                                    {
                                        icon: "diagram-2",
                                        label: builder.Locale.get("Related"),
                                    },
                                    function(tab,nav){
                                        tab.addClass('px-4 py-3');
                                        card.related = tab;
                                        RelationshipFeed(relationship, tab, table, record.id, function(feed){
                                            card.related.feed = feed;
                                        });
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
