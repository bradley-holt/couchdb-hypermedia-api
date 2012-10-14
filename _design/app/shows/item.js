function(doc, req) {
    registerType("hal", "application/hal+json");
    provides("hal", function() {
        return {
            "headers": {
                "Content-Type": "application/hal+json",
                "Vary": "Accept"
            },
            "body": JSON.stringify(doc.resource)
        };
    });
}
