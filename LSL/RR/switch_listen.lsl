integer chan = -666;
string switch_type = "main";
integer isPhantom = FALSE;
integer glc;
default {
	state_entry() {
		string desc = llGetObjectDesc();
		if (llSubStringIndex(desc, ":") != -1) {
			list dl = llParseString2List(desc, [":"], []);
			chan = llList2Integer(dl, 0);
			switch_type = llList2String(dl, 1);
			if (llList2String(dl, 2) == "phantom") {
				isPhantom = TRUE;
			}
		}
		glc = llListen(chan, "","","");
	}
	listen(integer ch, string name, key id, string msg) {
		if (ch == chan && llGetOwnerKey(id) == llGetOwner()) {
			if (llToLower(msg) == switch_type) {
				llSetObjectName("Guide");
				llSetColor(<0,1,0>, ALL_SIDES);
				if (isPhantom == FALSE) {
					llSetPrimitiveParams([PRIM_PHANTOM, FALSE]);
				}else if (isPhantom == TRUE) {
					llSetPrimitiveParams([PRIM_PHANTOM, TRUE]);
				}
			}else{
				llSetObjectName("Alt Guide");
				llSetColor(<1,0,0>, ALL_SIDES);
				if (isPhantom == FALSE) {
					llSetPrimitiveParams([PRIM_PHANTOM, TRUE]);
				}else if (isPhantom == TRUE) {
					llSetPrimitiveParams([PRIM_PHANTOM, TRUE]);
				}
			}
		}
	}
	changed(integer c) {
		if (c & (CHANGED_INVENTORY|CHANGED_REGION_START)) {
			llResetScript();
		}
	}
}