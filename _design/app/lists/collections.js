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
                "self": { "href": "/" },
                "item": []
            },
            "title": "Collections"
        };
        var row;
        while (row = getRow()) {
            collections._links.item.push({
                "href": "/" + row.key + "/",
                "title": row.value
            });
        }
        send(toJSON(collections));
        send("\n");
    });
}
