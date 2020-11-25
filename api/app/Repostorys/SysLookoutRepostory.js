'use strict'

class SysLookoutRepostory {
    static async getCallStatusContent(userID) {
        const DB = use('Database')
        const data1 =
            // await DB.raw('select CalledId, CallDuration, Seat, NormalCall ' +
            //     'from CallState with (nolock) ' +
            //     'where CallDuration=? ' +
            //     'and (ExtensionNo=\'\' or ExtensionNo is null) ' +
            //     'and UserID=?', [0, userID])
            await DB.table('CallState').select('CalledId', 'CallDuration', 'Seat', 'NormalCall')
                .where('CallDuration', '0')
                .where(function () {
                    this.where('ExtensionNo', '')
                        .orWhere('ExtensionNo', null)
                })
                .where('UserID', userID)

        const data2 =
            // await DB.raw('select a.ExtensionNo, a.CalledId, a.CalloutGroupID, a.CallDuration, b.PingTime, a.Seat, a.NormalCall, a.OnMonitor ' +
            //     'from CallState as a with (nolock) ' +
            //     'left join RegisteredLogs as b on a.ExtensionNo = b.CustomerNO ' +
            //     'where CallDuration > ? and ' +
            //     '(ExtensionNo <> ? or ExtensionNo is not null) and ' +
            //     'UserID=?', [0, '', userID])
            await DB.table('CallState').select('ExtensionNo', 'CalledId', 'CalloutGroupID', 'CallDuration', 'PingTime', 'Seat', 'NormalCall', 'OnMonitor')
                .leftJoin('RegisteredLogs', 'ExtensionNo', 'CustomerNO')
                .where('CallDuration', '>', 0)
                .where(function () {
                    this.where('ExtensionNo', '<>', '').orWhere(function () {
                        this.whereNotNull('ExtensionNo')
                    })
                })
                .where('UserID', userID)

        const data3 =
            // await DB.raw('select UserID, CallOutID, PlanName,StartCalledNumber, CalledCount, CalloutCount,CallConCount, CallSwitchCount, UseState,NumberMode, CalloutGroupID,ConcurrentCalls ' +
            //     'from CallPlan with (nolock) ' +
            //     'where UserID=?', [userID])
            await DB.table('CallPlan')
                .select('UserID', 'CallOutID', 'PlanName', 'StartCalledNumber', 'EndCalledNumber', 'CalledCount', 'CalloutCount', 'CallConCount', 'CallSwitchCount', 'UseState', 'NumberMode', 'CalloutGroupID', 'ConcurrentCalls')
                .where('UserID', userID)

        const waitExtensionNoCount =
            // await DB.raw('SELECT count(*) as count from CallState WHERE CallDuration>1 AND ExtensionNo=? AND UserID=?', ['system', userID])
            await DB.table('CallState')
                .where('CallDuration', '>', 1)
                .where('ExtensionNo', 'system')
                .where('UserID', userID)
                .count()

        const extensionNoCount =
            // await DB.raw('SELECT count(*) as count from CallState WHERE CallDuration>0 AND (ExtensionNo is not NULL OR ExtensionNo<>? )  AND UserID=?', ['', userID])
            await DB.table('CallState')
                .where('CallDuration', '>', 0)
                .where(function () {
                    this.whereNotNull('ExtensionNo')
                        .orWhere('ExtensionNo', '<>', '')
                })
                .where('UserID', userID)
                .count()
        const res =
            // await DB.raw('select Balance, Suspend from SysUser with (nolock) where UserID=?', [userID])
            await DB.table('SysUser')
                .select('Balance', 'Suspend')
                .where('UserID', userID)
        return {
            data1,
            data2,
            data3,
            waitExtensionNoCount: waitExtensionNoCount[0].count,
            extensionNoCount: extensionNoCount[0].count,
            balance: res[0].Balance.toFixed(2),
            suspend: res[0].Suspend != '1'
        }
    }
}

module.exports = SysLookoutRepostory
