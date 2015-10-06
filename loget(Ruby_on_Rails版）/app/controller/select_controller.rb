class SelectController < ApplicationController
  def top
    @users = []
    @ulda = People.find :all,:attributes => ["uid"]
    @users = @ulda
    @size = @users.size
    @spasan = Select.select("toJID")
  end

  def logall
    @items = params[:item]
    @logall = Select.order("ofMessageArchive3.sentDate asc").page(params[:page]).per(@items)
  end

  def logsearch
  # 入力フォームから入力されたものを格納する変数
    @items = params[:item]
    @sfromJID = params[:search_fromJID]
    @stoJID = params[:search_toJID]
    @sword = params[:search_word]
    @year1 = params[:se_date1]['d1(1i)']
    @month1 = params[:se_date1]['d1(2i)']
    @day1 = params[:se_date1]['d1(3i)']
    @year2 = params[:se_date2]['d2(1i)']
    @month2 = params[:se_date2]['d2(2i)']
    @day2 = params[:se_date2]['d2(3i)']

  # フォームから入力された時間をタイムスタンプ(10桁）にする必要がある。以下でその処理
    @jns1 = Time.mktime(@year1,@month1,@day1).to_i
    @jns2 = Time.mktime(@year2,@month2,@day2).to_i
  # そして、次にタイムスタンプ形式になっている上の二つをミリ時間に（13桁）に直す処理
    @miri = "000" # 下3桁に000を追加するための変数
    @jans1 = @jns1.to_s.concat(@miri) #　一度文字列にして結合
    @jans2 = @jns2.to_s.concat(@miri)
    @ans1 = @jans1.to_i #　比較は数字になるので、数列に変換
    @ans2 = @jans2.to_i

  # 検索とページングの処理
    @nday = @day2.to_i + 1
    @nday2 = @nday.to_s
    @njns2 = Time.mktime(@year2,@month2,@nday2).to_i
    @njans2 = @njns2.to_s.concat(@miri)
    @nans2 = @njans2.to_i
 if @ans1 > @ans2 then #fromが未来を指した場合
    @logsearch = Select.where('fromJID like ? and toJID like ? and body like ? and sentDate >= ? and sentDate <= ?', "%"+@sfromJID+"%","%"+@stoJID+"%","%"+@sword+"%",@ans2,@ans1).order("ofMessageArchive3.sentDate asc").page(params[:page]).per(@items)
 elsif @ans1 == @ans2
   @logsearch = Select.where('fromJID like ? and toJID like ? and body like ? and sentDate >= ? and sentDate <= ?', "%"+@sfromJID+"%","%"+@stoJID+"%","%"+@sword+"%",@ans1,@nans2).order("ofMessageArchive3.sentDate asc").page(params[:page]).per(@items)
  else # 通常
  @logsearch = Select.where('fromJID like ? and toJID like ? and body like ? and sentDate >= ? and sentDate <= ?', "%"+@sfromJID+"%","%"+@stoJID+"%","%"+@sword+"%",@ans1,@ans2).order("ofMessageArchive3.sentDate asc").page(params[:page]).per(@items)
 end
end
end
