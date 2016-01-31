var Messages = new Meteor.Collection('messages');

if (Meteor.isClient) {
  var maxrec = 50;
  Template.contents.max = maxrec;

  var colors = [
  'Cornsilk','Aquamarine','LightPink','PaleGreen','Coral',
  'SpringGreen','Plum','LightSkyBlue','MistyRose','Turquoise'
  ];
  var color = "background-color:"+colors[(+new Date()) % 10]+";";
  Template.contents.tcolor = color;    // 背景色表示

  Template.contents.messages = function () {
    return Messages.find({},{sort:{date:-1}});
  };

  // イベント処理
  Template.contents.events({
    'keydown input':function(ev){
      if(ev.keyCode == 13){ // 'enter' keyで確定
        var message = $("#postmes").val();
        if(message != ""){ //message（skypeIDが記入されていたらってこと） 
          var setdata = createProperties(message); // データをつくる
          // コレクションへ新レコードを登録
          Messages.insert(setdata,function(err,_id){
            // 最大件数以上なら古いのを消す
            var len = Messages.find({}).fetch().length;
            if(len > maxrec){
              var doc = Messages.findOne({}); // 先頭レコード(つまり一番古いレコード)を取り出す
              Messages.remove({_id:doc._id}); // そして消す
            }
          });
        }
      } 
     }
  });

  // messageレコードのプロパティ生成
  function createProperties(message){
    var date = Date.parse(new Date());
    var datetime = toDateStr(date);
    var name = $("#postname").val();
    var voice = $("#postvoice").val();
    var style = color;
    if(name == ""){
      name = "いつでも";
    }
    return {date:date,datetime:datetime,message:message,name:name,style:style,voice:voice};
}

  // 日付を文字列に変換
  function toDateStr(parse){
    var date = new Date(parse);
    var y = date.getFullYear();
    var m = date.getMonth()+1;
    var d = date.getDate();
    var h = date.getHours();
    var min = date.getMinutes();
    return y+"/"+m+"/"+d+" "+h+":"+min;
  }
};

if (Meteor.isServer) {
  Meteor.startup(function () {
    // code to run on server at startup
  });
}

