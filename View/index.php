<div class="col-12" id="layout"></div>
<script>
    $(document).ready(function(){

        // Ajax Request
        $.ajax({
            url: '/api/profile/fetch',
            type: 'GET',dataType: 'json',
            success: function(response) {

                // Configure Storage
                builder.Storage.setKey('profile');
                builder.Storage.set(response);
                console.log(builder.Storage.get())

                // Set the element
                var element = $('#layout');

                // Setup the layout
                element.row = $(document.createElement('div')).addClass('row').appendTo(element);
                element.col1 = $(document.createElement('div')).addClass('col-12 col-md-6 col-lg-4').appendTo(element.row);
                element.col2 = $(document.createElement('div')).addClass('col-12 col-md-6 col-lg-8').appendTo(element.row);

                var contacts = [];
                for(const [key, value] of Object.entries(response.dependencies.contacts ?? {})){
                    var text = value.vcard.name;
                    if(value.vcard.title != null){
                        text += ' - ' + value.vcard.title;
                    }
                    contacts.push({id:value.vcard.id,text:text});
                }

                // Create the Profile Card
                builder.Component(
                    "card",
                    element.col1,
                    {
                        icon: "person",
                        title: builder.Locale.get('Details'),
                    },
                    function(card,component){

                        // Styling
                        component.body.addClass('d-flex flex-column justify-content-center align-items-center');

                        // Insert the user's profile picture
                        component.body.avatar = $(document.createElement('div')).addClass('rounded-circle border border-3 border-light d-flex justify-content-center align-items-center position-relative').css({"height": "256px", "width": "256px"}).appendTo(component.body);
                        component.body.avatar.img = $(document.createElement('img')).attr({
                            "class": "rounded-circle",
                            "src": '/avatar?username=<?= $this->Auth->user()->username ?>&size=256',
                            "data-type": "avatar",
                            "data-vcard": response.record.vcard.id,
                            "style": "max-height: 250px; max-width: 250px; height: 250px; width: 250px; object-fit: contain; object-position: center;",
                        }).appendTo(component.body.avatar);
                        component.body.avatar.btn = $(document.createElement('button')).attr({
                            "type": "button",
                            "class": "btn btn-sm btn-info fs-5 rounded-circle position-absolute",
                            "style": "transition: all 0.5s ease-in-out; height: 48px!important; width: 48px!important; bottom: 8px; right: 8px;",
                        }).html('<i class="bi bi-upload"></i>').appendTo(component.body.avatar);
                        component.body.avatar.btn.click(function(){
                            vCardModalAvatar(response.record.vcard);
                        });

                        // Insert the user's name
                        component.body.name = $(document.createElement('div')).addClass('position-relative mt-2 text-center').appendTo(component.body);
                        component.body.name.string = $(document.createElement('h2')).addClass('fw-lighter m-0').text(response.record.vcard.name).appendTo(component.body.name);
                        component.body.name.btn = $(document.createElement('button')).attr({
                            "type": "button",
                            "class": "btn btn-sm btn-warning fs-5 rounded-circle position-absolute",
                            "style": "transition: all 0.5s ease-in-out; height: 48px!important; width: 48px!important; top: calc(50% - 24px); right: -56px;",
                        }).html('<i class="bi bi-pencil"></i>').appendTo(component.body.name);
                        component.body.name.btn.click(function(){
                            vCardModalEdit(response.record.vcard);
                        });

                        // Insert the user's organization
                        component.body.organization = $(document.createElement('div')).addClass('position-relative mt-2 text-center').appendTo(component.body);
                        component.body.organization.string = $(document.createElement('h4')).addClass('fw-lighter m-0').text(response.record.organization.vcard.name).appendTo(component.body.organization);
                        component.body.organization.btn = $(document.createElement('button')).attr({
                            "type": "button",
                            "class": "btn btn-sm btn-primary fs-5 rounded-circle position-absolute",
                            "style": "transition: all 0.5s ease-in-out; height: 48px!important; width: 48px!important; top: calc(50% - 24px); right: -56px;",
                        }).html('<i class="bi bi-person-vcard"></i>').appendTo(component.body.organization);
                        component.body.organization.btn.click(function(){
                            vCardModal(response.record.organization.vcard.id);
                        });
                    },
                );

                // Create a Tabs component
                builder.Component(
                    "tabs",
                    element.col2,
                    {
                        class: {
                            navbar: 'nav-pills',
                        },
                    },
                    function(tabs,card){
                        card._component.body.removeClass('card-body');
                        tabs.add(
                            'notes',
                            {
                                icon: "stickies",
                                label: builder.Locale.get("Notes"),
                            },
                            function(tab,nav){
                                card.notes = tab;
                                NotesFeed(builder.Storage.get('dependencies:notes') ?? {}, tab, 'users', response.record.id);
                            },
                        );
                        tabs.add(
                            'contacts',
                            {
                                icon: "person-vcard",
                                label: builder.Locale.get("Contacts"),
                            },
                            function(tab,nav){
                                card.contacts = tab;
                                ContactsFeed(response.dependencies.contacts ?? {}, tab, {
                                    "address": response.record.vcard.address,
                                    "city": response.record.vcard.city,
                                    "country": response.record.vcard.country,
                                    "state": response.record.vcard.state,
                                    "zipcode": response.record.vcard.zipcode,
                                    "locale": response.record.vcard.locale,
                                    "phone": response.record.vcard.phone,
                                    "targetTable": "users",
                                    "targetId": response.record.id,
                                });
                            },
                        );
                        tabs.add(
                            'activities',
                            {
                                icon: "activity",
                                label: builder.Locale.get("Activity"),
                            },
                            function(tab,nav){
                                tab.addClass('px-4 py-3');
                                card.activities = tab;
                                EventFeed(builder.Storage.get('dependencies:event') ?? {}, tab);
                            },
                        );
                        tabs.add(
                            'related',
                            {
                                icon: "diagram-2",
                                label: builder.Locale.get("Related"),
                            },
                            function(tab,nav){
                                tab.addClass('px-4 py-3');
                                RelationshipFeed(builder.Storage.getKey(), tab, function(feed){
                                    // card.related.feed = feed;
                                });
                            },
                        );
                    },
                );
            }
        });
    });
</script>
