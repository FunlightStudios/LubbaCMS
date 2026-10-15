using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Avatar
{
    /// <summary>
    /// Sends figure update confirmation to the user who changed their look
    /// Header: 2429 (USER_FIGURE / FigureUpdateEvent)
    /// Format: [figure:string][gender:string]
    /// </summary>
    public class NitroFigureUpdateComposer
    {
        public static NitroServerPacket Compose(string figure, string gender)
        {
            var packet = new NitroServerPacket(2429);
            packet.WriteString(figure);
            packet.WriteString(gender.ToUpper());
            return packet;
        }
    }
}
