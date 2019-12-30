/*
(c) 2005-2020 by Martin Willisegger
Project   : NagiosQL
Component : common JavaScript functions
Website   : https://sourceforge.net/projects/nagiosql/
Version   : 3.4.1
GIT Repo  : https://gitlab.com/wizonet/NagiosQL
 */
let popup = false;
function info(key1,key2,ver) {
    if(popup && popup.closed === false) popup.close();
    const top = (screen.availHeight - 240) / 2;
    const left = (screen.availWidth - 320) / 2;
    popup = window.open("info.php?key1=" + key1 + "&key2=" + key2 + "&version=" + ver,
        "Information",
        "width=320, height=240, top=" + top + ", left=" + left + ", SCROLLBARS=YES, MERNUBAR=NO, DEPENDENT=YES");
    popup.focus();
}

const myFocusObject = {};

function checkfields(fields,frm,object) {
    const ar_field = fields.split(",");
    for (let i=0;i<ar_field.length;i++){
        if (frm[ar_field[i]].value === "") {
            //frm[ar_field[i]].focus();
            object.myValue = frm[ar_field[i]];
            return false;
        }
    }
    return true;
}
function checkfields2(fields,frm,object) {
    const ar_field = fields.split(",");
    for (let i=0;i<ar_field.length;i++){
        if ((frm[ar_field[i]].value === "") || (frm[ar_field[i]].value === "0")) {
            //frm[ar_field[i]].focus();
            object.myValue = frm[ar_field[i]];
            return false;
        }
    }
    return true;
}
function checkboxes(fields,frm) {
    let retval = false;
    const ar_field = fields.split(",");
    for (let i=0;i<ar_field.length;i++){
        if (frm[ar_field[i]].checked === true) {
            retval = true;
        }
    }
    return retval;
}

<!-- YUI message box -->
function msginit(msg,header,type) {
    let iconobj;
    YAHOO.namespace("msg.container");
    const handleOK = function () {
        this.hide();
        //myFocusObject.myValue.focus();
    };
    if (type === 1) {
        iconobj = YAHOO.widget.SimpleDialog.ICON_WARN;
    }
    if (type === 2) {
        iconobj = YAHOO.widget.SimpleDialog.ICON_HELP;
    }
    YAHOO.msg.container.domainmsg = new YAHOO.widget.SimpleDialog("domainmsg",
        { width: "300px",
            fixedcenter: true,
            visible: false,
            draggable: false,
            close: true,
            text: msg,
            modal: true,
            icon: iconobj,
            constraintoviewport: true,
            buttons: [ { text:"Ok", handler:handleOK, isDefault:true } ]
        } );
    YAHOO.msg.container.domainmsg.setHeader(header);
    YAHOO.msg.container.domainmsg.render("msgcontainer");
    YAHOO.msg.container.domainmsg.show();
}

<!-- YUI confirm box -->
function confirminit(msg,header,type,yes,no,key) {
    let iconobj;
    YAHOO.namespace("question.container");
    const handleYes = function () {
        // noinspection JSUnresolvedFunction
        confOpenerYes(key);
        this.hide();
    };
    const handleNo = function () {
        this.hide();
    };
    if (type === 1) {
        iconobj = YAHOO.widget.SimpleDialog.ICON_WARN;
    }
    YAHOO.question.container.domainmsg = new YAHOO.widget.SimpleDialog("confirm1",
        { width: "400px",
            fixedcenter: true,
            visible: false,
            draggable: false,
            close: true,
            text: msg,
            modal: true,
            icon: iconobj,
            constraintoviewport: true,
            buttons: [ { text:yes, handler:handleYes, isDefault:true },
                { text:no, handler:handleNo }]
        } );
    YAHOO.question.container.domainmsg.setHeader(header);
    YAHOO.question.container.domainmsg.render("confirmcontainer");
    YAHOO.question.container.domainmsg.show();
}


<!-- YUI dialog box -->
function dialoginit(key1,key2,ver,header) {
    YAHOO.namespace("dialog.container");

    const handleCancel = function () {
        this.cancel();
    };
    const handleSuccess = function (o) {
        if (o.responseText !== undefined) {
            document.getElementById('dialogcontent').innerHTML = o.responseText;
        }
    };
    const handleFailure = function (o) {
        if (o.responseText !== undefined) {
            document.getElementById('dialogcontent').innerHTML = "No information found";
        }
    };
    const callback = {
        success: handleSuccess,
        failure: handleFailure
    };
    let sUrl;
    if (key2 === "updInfo") {
        sUrl = "admin/info.php?key1=" + key1 + "&key2=" + key2 + "&version=" + ver;
    } else {
        sUrl = "info.php?key1=" + key1 + "&key2=" + key2 + "&version=" + ver;
    }

    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);

    if (typeof YAHOO.dialog.container.infodialog === "undefined") {
        YAHOO.dialog.container.infodialog = new YAHOO.widget.Dialog("infodialog",
            { width : "50em",
                visible : false,
                draggable: true,
                fixedcenter: true,
                constraintoviewport : true,
                buttons : [ { text:"Ok", handler:handleCancel, isDefault:true } ]
            });

    }

    YAHOO.dialog.container.infodialog.setHeader(header);
    YAHOO.dialog.container.infodialog.render();
    YAHOO.dialog.container.infodialog.show();
}

<!-- YUI calendar -->
function calendarinit(lang,start,field,key,cont,obj) {
    YAHOO.util.Event.onDOMReady(function(){

        let dialog, calendar;

        calendar = new YAHOO.widget.Calendar(obj, {
            iframe:false,
            hide_blank_weeks:true,
            START_WEEKDAY:start
        });
        if (lang === "de_DE") {
            calendar.cfg.setProperty("MONTHS_LONG",    ["Januar", "Februar", "M\u00E4rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"]);
            calendar.cfg.setProperty("WEEKDAYS_SHORT", ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"]);
        }

        //function cancelHandler() {
        //   this.hide();
        //}

        //function handleSelect(type,args,obj) {
        function handleSelect(type,args) {
            const dates = args[0];
            const date = dates[0];
            const year = date[0];
            let month = date[1], day = date[2];

            const txtDate1 = document.getElementById(field);
            if (month < 10) { month = "0" + month;}
            if (day < 10)   { day = "0" + day;}
            // noinspection JSUndefinedPropertyAssignment
            txtDate1.value = year + "-" + month + "-" + day;
            dialog.hide();
        }

        dialog = new YAHOO.widget.Dialog(cont, {
            context:[field, "tl", "bl"],
            width:"16em",
            draggable:true,
            close:true
        });
        calendar.render();
        dialog.render();
        dialog.hide();

        calendar.renderEvent.subscribe(function() {
            dialog.fireEvent("changeContent");
        });
        // noinspection JSUnresolvedVariable
        calendar.selectEvent.subscribe(handleSelect, calendar.cal1, true);

        YAHOO.util.Event.on(key, "click", dialog.show, dialog, true);
    });
}

// Open edit dialog for list boxes
function openMutDlgInit(field,divbox,header,key,langkey1,langkey2,exclude) {

    YAHOO.util.Event.onDOMReady(function(){

        let mutdialog;

        const handleSuccess = function (o) {
            if (o.responseText !== undefined) {
                document.getElementById(divbox + 'content').innerHTML = o.responseText;
            }
        };
        const handleFailure = function (o) {
            if (o.responseText !== undefined) {
                document.getElementById(divbox + 'content').innerHTML = "No information found";
            }
        };
        const callback = {
            success: handleSuccess,
            failure: handleFailure
        };
        let sUrl;
        sUrl = "mutdialog.php?object=" + field + "&exclude=" + exclude;
        YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);

        const handleSave = function () {
            const source = document.getElementById(field);
            const targetSelect = document.getElementById(field + 'Selected');
            //const targetAvail = document.getElementById(field + 'Avail');
            for (let i = 0; i < targetSelect.length; ++i) {
                targetSelect.options[i].selected = true;
            }
            for (let i = 0; i < source.length; ++i) {
                source.options[i].selected = false;
                source.options[i].className = source.options[i].className.replace(/ ieselected/g, '');
            }
            for (let i = 0; i < targetSelect.length; ++i) {
                for (let y = 0; y < source.length; ++y) {
                    const value1 = targetSelect.options[i].value.replace(/^e/g, '');
                    const value2 = "e" + value1;
                    if ((source.options[y].value === value1) || (source.options[y].value === value2)) {
                        source.options[y].selected = true;
                        source.options[y].value = targetSelect.options[i].value;
                        source.options[y].text = targetSelect.options[i].text;
                        source.options[y].className = source.options[y].className + " ieselected";
                    }
                }
            }
            this.cancel();
            // noinspection JSUnresolvedVariable
            if ((typeof(update) === 'number') && (update === 1)) {
                // noinspection JSUnresolvedFunction
                updateForm(field);
            }
        };
        const handleCancel = function () {
            this.cancel();
        };
        mutdialog = new YAHOO.widget.Dialog(divbox,
            { width : "60em",
                fixedcenter : true,
                visible : false,
                draggable: true,
                modal: true,
                constraintoviewport : true,
                buttons : [ { text:langkey1, handler:handleSave, isDefault:true },
                    { text:langkey2, handler:handleCancel } ]
            });

        mutdialog.setHeader(header);
        mutdialog.render();
        mutdialog.hide();
        mutdialog.beforeShowEvent.subscribe(function() {
            getData(field);
        });

        YAHOO.util.Event.on(key, "click", mutdialog.show, mutdialog, true);
    });
}

// Additional functions for edit dialog
function getData(field) {
    const source = document.getElementById(field);
    const targetSelect = document.getElementById(field + 'Selected');
    const targetAvail = document.getElementById(field + 'Avail');
    for (let i=0; i < targetSelect.length; i++) {
        targetSelect.options[i] = null;
    }
    // noinspection JSUndefinedPropertyAssignment
    targetSelect.length = 0;
    for (let i=0; i < targetAvail.length; i++) {
        targetAvail.options[i] = null;
    }
    // noinspection JSUndefinedPropertyAssignment
    targetAvail.length = 0;
    let NeuerEintrag1;
    let NeuerEintrag2;
    for (let i = 0; i < source.length; ++i) {
        if (source.options[i].selected === true) {
            NeuerEintrag1 = new Option(source.options[i].text, source.options[i].value, false, false);
            NeuerEintrag1.className = source.options[i].className.replace(/ ieselected/g, '');
            NeuerEintrag1.className = NeuerEintrag1.className.replace(/ inpmust/g, '');
            targetSelect.options[targetSelect.length] = NeuerEintrag1;
        }
        if (source.options[i].selected === false) {
            if (source.options[i].text !== "") {
                NeuerEintrag2 = new Option(source.options[i].text, source.options[i].value, false, false);
                NeuerEintrag2.className = source.options[i].className.replace(/ ieselected/g, '');
                NeuerEintrag2.className = NeuerEintrag2.className.replace(/ inpmust/g, '');
                targetAvail.options[targetAvail.length] = NeuerEintrag2;
            }
        }
    }
}
// Insert selection
function selValue(field) {
    const targetSelect = document.getElementById(field + 'Selected');
    const targetAvail = document.getElementById(field + 'Avail');
    let NeuerEintrag;
    if (targetAvail.selectedIndex !== -1) {
        const DelOptions = [];
        for (let i = 0; i < targetAvail.length; ++i) {
            if (targetAvail.options[i].selected === true) {
                NeuerEintrag = new Option(targetAvail.options[i].text, targetAvail.options[i].value, false, false);
                NeuerEintrag.className = targetAvail.options[i].className;
                targetSelect.options[targetSelect.length] = NeuerEintrag;
                DelOptions.push(i);
            }
        }
        sort(targetSelect);
        DelOptions.reverse();
        for (let i=0; i<DelOptions.length; ++i) {
            targetAvail.options[DelOptions[i]] = null;
        }
    }
}
// Insert selection (exclude variant)
function selValueEx(field) {
    const targetSelect = document.getElementById(field + 'Selected');
    const targetAvail = document.getElementById(field + 'Avail');
    let NeuerEintrag;
    if (targetAvail.selectedIndex !== -1) {
        const DelOptions = [];
        for (let i = 0; i < targetAvail.length; ++i) {
            if (targetAvail.options[i].selected === true) {
                if ((targetAvail.options[i].text !== '*') && (targetAvail.options[i].value !== '0')) {
                    NeuerEintrag = new Option("!" + targetAvail.options[i].text, "e" + targetAvail.options[i].value, false, false);
                } else {
                    NeuerEintrag = new Option(targetAvail.options[i].text, targetAvail.options[i].value, false, false);
                }
                NeuerEintrag.className = targetAvail.options[i].className;
                targetSelect.options[targetSelect.length] = NeuerEintrag;
                DelOptions.push(i);
            }
        }
        sort(targetSelect);
        DelOptions.reverse();
        for (let i = 0; i < DelOptions.length; ++i) {
            targetAvail.options[DelOptions[i]] = null;
        }
    }
}
// Remove selection
function desValue(field) {
    const targetSelect = document.getElementById(field + 'Selected');
    const targetAvail = document.getElementById(field + 'Avail');
    let NeuerEintrag;
    if (targetSelect.selectedIndex !== -1) {
        const DelOptions = [];
        for (let i = 0; i < targetSelect.length; ++i) {
            if (targetSelect.options[i].selected === true) {
                const text = targetSelect.options[i].text.replace(/^!/g, '');
                const value = targetSelect.options[i].value.replace(/^e/g, '');
                NeuerEintrag = new Option(text, value, false, false);
                NeuerEintrag.className = targetSelect.options[i].className;
                targetAvail.options[targetAvail.length] = NeuerEintrag;
                DelOptions.push(i);
            }
        }
        sort(targetAvail);
        DelOptions.reverse();
        for (let i = 0; i < DelOptions.length; ++i) {
            targetSelect.options[DelOptions[i]] = null;
        }
    }
}
// Sort entries
function sort(obj){
    const sortieren = [];
    const list = [];
    let i;

    // Insert list to array
    for (i=0; i < obj.options.length; i++) {
        list[i] = [];
        list[i]["text"] = obj.options[i].text;
        list[i]["value"] = obj.options[i].value;
        list[i]["className"] = obj.options[i].className;
    }

    // Sort into a single dimension array
    for (i=0; i < obj.length; i++){
        sortieren[i]=list[i]["text"]+";"+list[i]["value"]+";"+list[i]["className"];
    }

    // Real sort
    sortieren.sort();

    // Make array to list
    for (i=0; i < sortieren.length; i++) {
        const felder = sortieren[i].split(";");
        list[i]["text"] = felder[0];
        list[i]["value"] = felder[1];
        list[i]["className"] = felder[2];
    }

    // Remove list field
    for (i=0; i < obj.options.length; i++) {
        obj.options[i] = null;
    }

    // insert list to dialog
    let NeuerEintrag;
    for (i = 0; i < list.length; i++) {
        NeuerEintrag = new Option(list[i]["text"], list[i]["value"], false, false);
        NeuerEintrag.className = list[i]["className"];
        obj.options[i] = NeuerEintrag;
    }
}
// Show relation data
function showRelationData(option) {
    if (option === 1) {
        document.getElementById("rel_text").className = "elementHide";
        document.getElementById("rel_info").className = "elementShow";
    } else {
        document.getElementById("rel_text").className = "elementShow";
        document.getElementById("rel_info").className = "elementHide";
    }
}