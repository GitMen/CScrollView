//
//  ViewController.swift
//  CScrollView
//
//  Created by 张鼎辉 on 15/4/11.
//  Copyright (c) 2015年 四海道达网络科技有限公司. All rights reserved.
//

import UIKit

class ViewController: UIViewController , CScrollViewDelegate{

    override func viewDidLoad() {
        super.viewDidLoad()
        self.view.backgroundColor = UIColor.brownColor()
        // Do any additional setup after loading the view, typically from a nib.
        
        //两种不同展示形式，可切换使用
//        self.webPageUrls()
        self.customerViews()

    }
    func webPageUrls(){
        let viewY:CGFloat = (UIScreen.mainScreen().bounds.size.height - 250)/2
        let sView:CScrollView = CScrollView(frame: CGRectMake(0, viewY , UIScreen.mainScreen().bounds.size.width, 250))
        
        
        let imageUrls:NSMutableArray = NSMutableArray(capacity: 42)
        imageUrls.addObject("http://img4.duitang.com/uploads/item/201407/13/20140713095140_LusMk.thumb.700_0.jpeg")
        imageUrls.addObject("http://img4.duitang.com/uploads/item/201407/06/20140706090107_H3Tyd.jpeg")
        imageUrls.addObject("http://img4q.duitang.com/uploads/item/201407/21/20140721143254_reBYa.jpeg")
        imageUrls.addObject("http://i2.img.969g.com/pcgame/imgx2015/03/24/289_143056_7f39a_lit.jpg")

        sView.imagesUrls = imageUrls
        
        //打开自动滚动
        sView.openAutomaticRolling()
        //打开长按停止自动滚动
        sView.openLongPanStopRolling(1)
        sView.csDelegate = self
        self.view.addSubview(sView)
    }
    
    func customerViews(){
        let viewY:CGFloat = (UIScreen.mainScreen().bounds.size.height - 250)/2
        let sView:CScrollView = CScrollView(frame: CGRectMake(0, viewY , UIScreen.mainScreen().bounds.size.width, 250))
        
        
        let view1:UIImageView = UIImageView(frame: CGRectMake(0, 0, sView.frame.size.width , 250))
        let view2:UIImageView = UIImageView(frame: CGRectMake(0, 0, sView.frame.size.width , 250))
        let view3:UIImageView = UIImageView(frame: CGRectMake(0, 0, sView.frame.size.width , 250))
        let view4:UIImageView = UIImageView(frame: CGRectMake(0, 0, sView.frame.size.width , 250))
        view1.image = UIImage(named: "1.jpg")
        view2.image = UIImage(named: "2.jpg")
        view3.image = UIImage(named: "3.jpg")
        view4.image = UIImage(named: "4.jpg")
        let subViews:NSMutableArray = NSMutableArray(capacity: 42)
        subViews.addObject(view1)
        subViews.addObject(view2)
        subViews.addObject(view3)
        subViews.addObject(view4)
        
        //通过自定义试图展示
        sView.subViews = subViews
        
        //打开自动滚动
        sView.openAutomaticRolling()
        //打开长按停止自动滚动
        sView.openLongPanStopRolling(1)
        sView.csDelegate = self
        self.view.addSubview(sView)
    }
    //MARK: 被点击时调用
    func cscrollViewOnClick(index: Int) {
        
        print("\(index)页面被点击了 \n")
    }
    //MARK: 滚动到某页
    func cscrollViewPageChange(index: Int) {
        print("当前在\(index)页面 \n")
    }
}

