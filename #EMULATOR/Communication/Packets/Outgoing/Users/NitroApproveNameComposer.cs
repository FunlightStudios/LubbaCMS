using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends Nitro pet/name approval result.
    /// Header: 1503 (APPROVE_NAME_RESULT)
    /// </summary>
    public class NitroApproveNameComposer : NitroServerPacket
    {
        public NitroApproveNameComposer(int result, string validationInfo)
            : base(1503)
        {
            WriteInteger(result);
            WriteString(validationInfo ?? string.Empty);
        }
    }
}
