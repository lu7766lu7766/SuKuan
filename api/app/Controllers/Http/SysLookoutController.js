'use strict'

class SysLookoutController {
    
    async ajaxCallStatusContent2({request}) {
        const SysLookoutRepo = use('App/Repostorys/SysLookoutRepostory')
        return await SysLookoutRepo.getCallStatusContent(request.input('userId'))
    }
}

module.exports = SysLookoutController
