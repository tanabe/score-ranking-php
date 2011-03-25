package {

  import flash.events.Event;

  /**
   *  ranking API connector event
   *  @author tanablog@gmail.com
   */
  public class RankingAPIConnectorEvent extends Event {

    public static const SEND_COMPLETE:String = "sendComplete";
    public static const GET_RANKING_COMPLETE:String = "getRankingComplete";
    public var result:XML;

    /**
     *  constructor
     *  @param type event type
     */
    public function RankingAPIConnectorEvent(type:String, result:XML = null) {
      super(type);
      this.result = result;
    }
  }
}
