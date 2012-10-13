function(head, req) {
    registerType("hal", "application/hal+json");
    provides("hal", function() {
        start({
            "headers": {
                "Content-Type": "application/hal+json",
                "Vary": "Accept"
            }
        });
        var resource = {};
        var row;
        while (row = getRow()) {
            if (0 == row.key[1]) {
                resource = row.value;
                resource._links.item = [];
            } else if (1 == row.key[1] && resource._links) {
                resource._links.item.push({
                    "href": "/api/" + row.id,
                    "title": row.value.title
                });
            }
        }
        send(JSON.stringify(resource));
        send("\n");
    });
}
