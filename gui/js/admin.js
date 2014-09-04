/** @file
 * Functions related to the administrator tab.
 *
 * Provides the user, project, and tagset editor.
 *
 * @author Marcel Bollmann
 * @date January 2012 - September 2014
 */

// ***********************************************************************
// ********** USER MANAGEMENT ********************************************
// ***********************************************************************

cora.users = {
    data: [],
    byID: {},
    onUpdateHandlers: [],

    /* Function: getAll

       Return an array containing all users.
    */
    getAll: function() {
        return this.data;
    },

    /* Function: onUpdate

       Add a callback function to be called whenever the user list is
       updated.

       Parameters:
        fn - function to be called
     */
    onUpdate: function(fn) {
        if(typeof(fn) == "function")
            this.onUpdateHandlers.push(fn);
        return this;
    },

    /* Function: createUser
       
       Sends a server request to create a new user.

       Parameters:
        name - Desired username
        pw   - Desired password
        fn   - Callback function to invoke after the request
    */
    createUser: function(name, pw, fn) {
        var ref = this;
	new Request.JSON(
	    {'url': 'request.php?do=createUser',
	     'async': false,
	     'data': {'username': name, 'password': pw},
	     onSuccess: function(status, text) {
		 ref.performUpdate();
                 if(typeof(fn) == "function")
                     fn(status, text);
	     }
	}).post();
    },

    /* Function: deleteUser

       Sends a server request to delete a user.

       Parameters:
        id - ID of the user to delete
        fn - Callback function to invoke after the request
     */
    deleteUser: function(id, fn) {
        var ref = this;
	new Request.JSON({
            'url': 'request.php?do=deleteUser',
	    'async': false,
	    'data': {'id': id},
	    onSuccess: function(status, text) {
                ref.performUpdate();
                if(typeof(fn) == "function")
                    fn(status, text);
	    }
	}).post();
    },

    /* Function: toggleAdmin

       Sends a server request to toggle admin status of a user.  Does
       not trigger functions in onUpdateHandler.

       Parameters:
        id - ID of the user
        fn - Callback function to invoke after the request
     */
    toggleAdmin: function(id, fn) {
        var ref = this;
	new Request.JSON({
            'url': 'request.php?do=toggleAdmin',
	    'async': false,
	    'data': {'id': id},
	    onSuccess: function(status, text) {
                if(status['success']) {
                    var value = ref.data[ref.byID[id]].admin=="1" ? "0" : "1";
                    ref.data[ref.byID[id]].admin = value;
                }
                if(typeof(fn) == "function")
                    fn(status, text);
            }
	}).post();
    },

    /* Function: changePassword

       Sends a server request to change the password of a user.

       Parameters:
        id - ID of the user
        pw - New password
        fn - Callback function to invoke after the request
    */
    changePassword: function(id, pw, fn) {
	new Request.JSON({
            'url': 'request.php?do=changePassword',
	    'async': false,
	    'data': {'id': id, 'password': pw},
	    onSuccess: function(status, text) {
                if(typeof(fn) == "function")
                    fn(status, text);
	    }
	}).post();
    },

    /* Function: performUpdate

       Perform a server request to update the user data.  Calls any
       handlers previously registered via onUpdate().
     */
    performUpdate: function() {
        var ref = this;
        new Request.JSON({
            url: 'request.php',
            onSuccess: function(status, text) {
                if(!status['success']) {
                    gui.showNotice('error',
                                   "Konnte Benutzerdaten nicht laden.");
                    return;
                }
                ref.data = status['data'];
                ref.byID = {};
                Array.each(ref.data, function(user, idx) {
                    ref.byID[user.id] = idx;
                });
                Array.each(ref.onUpdateHandlers, function(handler) {
                    handler(status, text);
                });
            }
        }).get({'do': 'getUserList'});
    }
}

cora.userEditor = {
    initialize: function() {
        cora.users.onUpdate(this.refreshUserTable);
        cora.users.performUpdate();

	var ceralink = new Element('a', {
	    "id": 'ceraCUButton',
	    "href": '#ceraCreateUser'
	}).wraps('createUser');
	ceralink.cerabox({
	    displayTitle: false,
	    group: false,
	    events: {
		onOpen: function(currentItem, collection) {
	    	    $$('button[name="submitCreateUser"]').addEvent(
			'click', cora.userEditor.createUser, cora.userEditor
		    );
		}
	    }
	});

	$('editUsers').addEvent(
	    'click:relay(td)',
	    function(event, target) {
		if(target.hasClass('adminUserAdminStatus')) {
		    cora.userEditor.toggleStatus(event, 'Admin');
		}
		else if(target.hasClass('adminUserDelete')) {
		    cora.userEditor.deleteUser(event);
		}
	    }
	);
    },
    createUser: function() {
	var username  = $$('.cerabox-content input[name="newuser[un]"]')[0].get('value');
	var password  = $$('.cerabox-content input[name="newuser[pw]"]')[0].get('value');
	var controlpw = $$('.cerabox-content input[name="newuser[pw2]"]')[0].get('value');

	// perform checks
	if (username === '') {
	    alert("Fehler: 'Benutzername' darf nicht leer sein.");
	    return false;
	}
	if (password === '') {
	    alert("Fehler: 'Passwort' darf nicht leer sein.");
	    return false;
	}
	if (password != controlpw) {
	    alert("Fehler: Passwörter stimmen nicht überein.");
	    return false;
	}

	// send request
        cora.users.createUser(username, password, function (status, text) {
	    CeraBoxWindow.close();
            if(status['success']) {
                gui.showNotice('ok', 'Benutzer hinzugefügt.');
            }
            else {
	        gui.showNotice('error', 'Benutzer nicht hinzugefügt.');
            }
        });
	return true;
    },
    deleteUser: function(event) {
	var parentrow = event.target.getParent('tr');
	var uid = parentrow.get('id').substr(5);
        var username = parentrow.getElement('td.adminUserNameCell').get('text');

	var dialog = "Soll der Benutzer '" + username + "' wirklich gelöscht werden?";
	if (!confirm(dialog))
	    return;

        cora.users.deleteUser(uid, function(status, text) {
            if(status['success']) {
                gui.showNotice('ok', 'Benutzer gelöscht.');
            }
            else {
                gui.showNotice('error', 'Benutzer nicht gelöscht.');
            }
        });
    },
    toggleStatus: function(event, statusname) {
        if(statusname!='Admin')
            return;
	var parentrow = event.target.getParent('tr');
	var uid = parentrow.get('id').substr(5);

        cora.users.toggleAdmin(uid, function(status, text) {
            if(!status['success']) {
                gui.showNotice('error', 'Admin-Status nicht geändert.');
            }
	    var arrow = parentrow.getElement('img.adminUserAdminStatus');
	    if(arrow.isDisplayed()) {
		arrow.hide();
	    } else {
		arrow.show('inline');
	    }
        });
    },
    changePassword: function() {
	var uid       = $$('.cerabox-content input[name="changepw[id]"]')[0].get('value');
	var password  = $$('.cerabox-content input[name="changepw[pw]"]')[0].get('value');
	var controlpw = $$('.cerabox-content input[name="changepw[pw2]"]')[0].get('value');

	// perform checks
	if (password === '') {
	    alert("Fehler: 'Passwort' darf nicht leer sein.");
	    return false;
	}
	if (password != controlpw) {
	    alert("Fehler: Passwörter stimmen nicht überein.");
	    return false;
	}

	// send request
        cora.users.changePassword(uid, password, function (status, text) {
	    CeraBoxWindow.close();
            if(status['success']) {
                gui.showNotice('ok', 'Password geändert.');
            }
            else {
                gui.showNotice('error', 'Password nicht geändert.');
            }
        });
	return true;
    },
    
    /* Function: refreshUserTable
       
       Renders the table containing the user data.
     */
    refreshUserTable: function() {
        var table = $('editUsers');
        table.getElements('tr.adminUserInfoRow').dispose();
        Array.each(cora.users.getAll(), function(user) {
            var tr = $('templateUserInfoRow').clone();
            tr.set('id', 'User_'+user.id);
            tr.getElement('td.adminUserNameCell').set('text', user.name);
            tr.getElement('td.adminUserLastactiveCell').set('text', user.lastactive);
            if(user.active == "1")
                tr.addClass('userActive');
            if(user.admin == "0")
                tr.getElement('img.adminUserAdminStatus').hide();
            tr.inject(table);
        });

        // old legacy code here:
	$$('button.adminUserPasswordButton').each(
	    function(button) {
		var uid = button.getParent('tr').get('id').substr(5);
		var ceralink = new Element('a', {'href':'#ceraChangePassword'}).wraps(button);
		ceralink.cerabox({
		    displayTitle: false,
		    group: false,
		    events: {
			onOpen: function(currentItem, collection) {
			    $$('input[name="changepw[id]"]').set('value', uid);
			    $$('button[name="submitChangePassword"]').addEvent(
				'click', cora.userEditor.changePassword, cora.userEditor
			    );
			}
		    }
		});
	    }
	);
    },
}

cora.projectEditor = {
    initialize: function() {
	var ref = this;
        cora.projects.onUpdate(function(status, text) {
            ref.updateProjectUserInfo();
        });
	// adding projects
	var cp_mbox = new mBox.Modal({
	    'title': 'Neues Projekt erstellen',
	    'content': 'projectCreateForm',
	    'attach': 'createProject'
	});
	$('projectCreateForm').getElement("button[name='submitCreateProject']").addEvent(
	    'click',
	    function(event, target) {
		var pn = $('projectCreateForm').getElement('input').get('value');
		var req = new Request.JSON(
		    {url:'request.php',
		     onSuccess: function(status) {
			 var pid = false;
			 if(status!==null && status.success && status.pid) {
			     pid = Number.from(status.pid);
			 }
			 if(!pid || pid<1) {
			     new mBox.Notice({
				 content: 'Projekt erstellen fehlgeschlagen',
				 type: 'error',
				 position: {x: 'right'}
			     });
			 } else {
			     pid = String.from(pid);
			     var new_row = $('editProjects').getElement('tr.adminProjectInfoRow').clone();
			     new_row.set('id', 'project_'+pid);
			     new_row.getElement('a.adminProjectDelete').set('id', 'projectdelete_'+pid);
			     new_row.getElement('td.adminProjectNameCell').empty().appendText(pn);
			     new_row.getElement('td.adminProjectUsersCell').empty();
			     new_row.getElement('button.adminProjectUsersButton').set('id', 'projectbutton_'+pid);
			     $('editProjects').adopt(new_row);
			     
			     $('projectCreateForm').getElement('input').set('value', '');
			     cp_mbox.close();
			     new mBox.Notice({
				 content: 'Projekt erfolgreich angelegt',
				 type: 'ok',
				 position: {x: 'right'}
			     });
			 }
		     }
		    });
		req.get({'do': 'createProject', 'project_name': pn});
	    }
	);

	// deleting projects
	$('editProjects').addEvent(
	    'click:relay(a)',
	    function(event, target) {
		if(target.hasClass("adminProjectDelete")) {
		    var pid = target.get('id').substr(14);
		    var pn  = target.getParent('tr').getElement('td.adminProjectNameCell').get('html');
		    if(file.fileHash == undefined) {
			file.listFiles();
		    }
		    if (file.fileHash[pid] == undefined
		      || Object.getLength(file.fileHash[pid]) == 0) {
			var req =  new Request.JSON(
			    {url:'request.php',
			     onSuccess: function(data, text) {
				 if(data.success) {
				     $('project_'+pid).dispose();
				     new mBox.Notice({
					 content: 'Projekt gelöscht',
					 type: 'ok',
					 position: {x: 'right'}
				     });
				 } else {
				     new mBox.Notice({
					 content: 'Projekt löschen fehlgeschlagen',
					 type: 'error',
					 position: {x: 'right'}
				     });
				 }
			     }
			    }
			);
			req.get({'do': 'deleteProject', 'project_id': pid});
	   
		    } else {
			new mBox.Modal({
			    'title': 'Projekt löschen: "'+pn+'"',
			    'content': 'Projekte können nicht gelöscht werden, solange noch mindestens ein Dokument dem Projekt zugeordnet ist.',
			    'buttons': [ {'title': 'OK'} ]
			}).open();
		    }
		}
	    }
	);
	// editing project groups
	$('editProjects').addEvent(
	    'click:relay(button)',
	    function(event, target) {
		if(target.hasClass("adminProjectUsersButton")) {
		    var mbox_content = $('projectUserChangeForm').clone();
		    var pid = target.get('id').substr(14);
		    var pn  = target.getParent('tr').getElement('td.adminProjectNameCell').get('html');
                    var prj = cora.projects.get(pid);
		    prj.users.each(function(user, idx) {
			mbox_content.getElement("input[value='"+user.name+"']").set('checked', 'checked');
		    });
		    mbox_content.getElement("input[name='project_id']").set('value', pid);
		    var mbox = new mBox.Modal({
			title: 'Benutzergruppe für Projekt "'+pn+'" bearbeiten',
			content: mbox_content,
		    });
		    new mForm.Submit({
			form: mbox_content.getElement('form'),
			timer: 0,
			showLoader: true,
			onComplete: function(response) {
			    response = JSON.decode(response);
			    if(response.success) {
				mbox.close();
				new mBox.Notice({
				    content: 'Benutzergruppe geändert',
				    type: 'ok',
				    position: {x: 'right'}
				});
				cora.projects.performUpdate();
			    } else {
				new mBox.Modal({
				    'title': 'Änderungen speichern nicht erfolgreich',
				    content: 'Ein unerwarteter Fehler ist aufgetreten. Bitte kontaktieren Sie einen Administrator.'
				}).open();
			    }
			}
		    });
		    mbox.open();
		}
	    }
	);
    },

    updateProjectUserInfo: function() {
	Object.each(cora.projects.getAll(), function(prj) {
	    var tr = $('project_'+prj.id);
            var ulist = prj.users.map(function(user) { return user.name; });
	    if(tr != undefined) {
		tr.getElement('td.adminProjectUsersCell').empty()
                    .appendText(ulist.join());
	    }
	});
    }
}


cora.tagsetEditor = {
    initialize: function() {
	var ref = this;
	this.activateImportForm();
	this.activateTagsetViewer();
    },

    activateTagsetViewer: function() {
	var ref = this;
	var import_mbox = new mBox.Modal({
	    title: 'Tagset-Browser',
	    content: 'adminTagsetBrowser',
	    attach: 'viewTagset'
	});

	$('aTBview').addEvent('click', function(e) {
	    var tagset     = $('aTBtagset').getSelected().get('value')[0];
	    var tagsetname = $('aTBtagset').getSelected().get('html');
	    var textarea   = $('aTBtextarea');
	    textarea.empty();
	    // fetch tag list and perform a quick and dirty analysis:
	    var request = new Request.JSON(
		{url:'request.php',
		 onSuccess: function(status, text) {
		     var output;
		     if(status['success']) {
			 var data = status['data'];
			 var postags = new Array();
			 output = tagsetname + " (ID: " + tagset + ") has ";
			 output += data.length + " tags ";
			 Array.each(data, function(tag) {
			     var pos;
			     var dot = tag['value'].indexOf('.');
			     if(dot>=0 && dot<tag['value'].length-1) {
				 pos = tag['value'].slice(0, dot);
			     } else {
				 pos = tag['value'];
			     }
			     postags.push(pos);
			 });
			 postags = postags.unique();
			 output += "in " + postags.length + " base POS categories.\n\n";
			 output += "Base POS categories are:\n";
			 output += postags.join(", ");
			 output += "\n\nAll tags:\n";
			 Array.each(data, function(tag) {
			     if(tag['needs_revision']==1) {
				 output += "^";
			     }
			     output += tag['value'] + "\n";
			 });
		     }
		     else {
			 output = "Fehler beim Laden des Tagsets.";
		     }
		     textarea.empty().appendText(output);
		 }
		}
	    );
	    request.get({'do': 'fetchTagset', 'tagset_id': tagset, 'limit': 'none'});
	});
    },

    activateImportForm: function() {
	var ref = this;
	var formname = 'newTagsetImportForm';
	var import_mbox = new mBox.Modal({
	    title: 'Tagset aus Textdatei importieren',
	    content: 'tagsetImportForm',
	    attach: 'importTagset'
	});

	// note: these checks would be redundant if the iFrame method
	// below would be replaced by an mForm.Submit ...
	// check if a name & file has been selected
	$('newTagsetImportForm').getElement('input[type="submit"]').addEvent('click', function(e) {
	    var importname = $('newTagsetImportForm').getElement('input[name="tagset_name"]').get('value');
	    if(importname==null || importname=="") {
		$('newTagsetImportForm').getElement('input[name="tagset_name"]').addClass("input_error");
		e.stop();
	    } else {
		$('newTagsetImportForm').getElement('input[name="tagset_name"]').removeClass("input_error");
	    }
	    var importfile = $('newTagsetImportForm').getElement('input[name="txtFile"]').get('value');
	    if(importfile==null || importfile=="") {
		$$('#newTagsetImportForm p.error_text').show();
		e.stop();
	    } else {
		$$('#newTagsetImportForm p.error_text').hide();
	    }
	});

        var iFrame = new iFrameFormRequest(formname, {
            onFailure: function(xhr) {
		// never fires?
       		alert("Import nicht erfolgreich: Der Server lieferte folgende Fehlermeldung zurück:\n\n" +
       		      xhr.responseText);
       	    },
	    onRequest: function(){
		import_mbox.close();
		gui.showSpinner({message: 'Importiere Tagset...'});
	    },
	    onComplete: function(response){
		var title="", message="", textarea="", error=false;
		try{
		    response = JSON.decode(response);
		}catch(err){
		    message =  "Der Server lieferte eine ungültige Antwort zurück.";
		    textarea  = "Antwort des Servers:\n";
		    textarea += response;
		    textarea += "\n\nInterner Fehler:\n";
		    textarea += err.message;
		    error = true;
		}
		
		if(error){
		    title = "Tagset-Import fehlgeschlagen";
		}
		else if(response==null){
		    title = "Tagset-Import fehlgeschlagen";
		    message = "Beim Import des Tagsets ist ein unbekannter Fehler aufgetreten.";
		}
		else if(!response.success){
		    title = "Tagset-Import fehlgeschlagen";
		    message  = "Beim Import des Tagsets ";
		    message += response.errors.length>1 ? "sind " + response.errors.length : "ist ein";
		    message += " Fehler aufgetreten:";

		    for(var i=0;i<response.errors.length;i++){
			textarea += response.errors[i] + "\n";
		    }
		} 
		else { 
		    title = "Tagset-Import erfolgreich";
		    message = "Das Tagset wurde erfolgreich hinzugefügt.";
		    if((typeof response.warnings !== "undefined") && response.warnings.length>0) {
			message += " Das System lieferte ";
			message += response.warnings.length>1 ? response.warnings.length + " Warnungen" : "eine Warnung";
			message += " zurück:";

			for(var i=0;i<response.warnings.length;i++){
			    textarea += response.warnings[i] + "\n";
			}
		    }

		    form.reset($(formname));
		    $(formname).getElements('.error_text').hide();
                }

		if(textarea!='') {
		    $('adminImportPopup').getElement('p').empty().appendText(message);
		    $('adminImportPopup').getElement('textarea').empty().appendText(textarea);
		    message = 'adminImportPopup';
		}
		new mBox.Modal({
		    title: title,
		    content: message,
		    closeOnBodyClick: false,
		    buttons: [ {title: "OK"} ]
		}).open();

		gui.hideSpinner();
            }
	});

    }
}

// ***********************************************************************
// ********** DOMREADY BINDINGS ******************************************
// ***********************************************************************

window.addEvent('domready', function() {
    cora.userEditor.initialize();
    cora.projectEditor.initialize();
    cora.tagsetEditor.initialize();
});


//    var mask = new Mask();
//    mask.show();
