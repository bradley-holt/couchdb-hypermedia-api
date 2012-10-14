function(head, req) {
    registerType("hal", "application/hal+json");
    provides("hal", function() {
        start({
            "headers": {
                "Content-Type": "application/hal+json",
                "Vary": "Accept"
            }
        });
        var collections = {
            "_links": {
                "self": { "href": "/api/" },
                "item": []
            },
            "title": "Collections"
        };
        var row;
        while (row = getRow()) {
            collections._links.item.push({
                "href": "/api/" + row.key,
                "title": row.value.title
            });
        }
        send(JSON.stringify(collections));
    });
}
