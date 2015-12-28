//
//  CustomerScrollTitleView.swift
//  CustomerScrollTitleViewDemo
//
//  Created by codeSimple on 15/3/17.
//  Copyright (c) 2015年
//

import UIKit
//  MARK: CScrollViewDelegate
@objc protocol CScrollViewDelegate : NSObjectProtocol{
    optional func cscrollViewPageChange(index:Int)
    optional func cscrollViewOnClick(index:Int)
}

//  MARK:
class CScrollView: UIView , UIScrollViewDelegate{
    //  设置公开变量
    internal var placeHoderImage: UIImage?
    internal var csDelegate: CScrollViewDelegate?
    internal var isImageSubView: Bool?
    internal var subViews: NSArray {
        didSet{
            self.isImageSubView = false
            self.configSubViews()
        }
    }
    internal var imagesUrls:NSArray{
        didSet{
            //监听imageUrl赋值方法
            self.isImageSubView = true
            self.configImageView()
        }
    }
    
    //设置私有变量
    private var mainView:UIScrollView?
    private var mViews:NSMutableArray?
    private var pageViewLeft:UIImageView?
    private var pageViewCenter:UIImageView?
    private var pageViewRight:UIImageView?
    private var isStopRoll:Bool?
    
    override init(frame: CGRect) {
        self.mViews = NSMutableArray(capacity: 42)
        self.imagesUrls = NSArray()
        self.subViews = NSArray()
        super.init(frame: frame)
        //创建主试图
        self.createMainView()
    }
    
    required init(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    //MARK: 开启自动滚动
    internal func openAutomaticRolling(){
        //设置定时器
        NSTimer.scheduledTimerWithTimeInterval(4, target: self, selector: "automaticRolling", userInfo: nil, repeats: true);
    }
    //MARK: 开启长按停止自动滚动
    internal func openLongPanStopRolling(panTime:NSTimeInterval){
        
        for var i = 0 ; i < self.mViews?.count ; i++ {
            let imageView : UIImageView =  self.mViews!.objectAtIndex(i) as! UIImageView
            let longPan:UILongPressGestureRecognizer = UILongPressGestureRecognizer(target: self, action: "longPan:");
            longPan.minimumPressDuration = panTime;
            imageView.addGestureRecognizer(longPan)
        }
        
    }
    
    //MARK: 自动滚动
    func automaticRolling(){
        if self.isStopRoll == true{
            return;
        }
        //滚动时停止交互
        self.mainView?.userInteractionEnabled = false;
        //固定向右滚动
        UIView.animateWithDuration(2, animations: { () -> Void in
            let frame:CGFloat = self.mainView!.contentOffset.x + self.mainView!.frame.size.width;
            self.mainView?.contentOffset = CGPointMake(frame, 0);
            }) { (Bool isOk) -> Void in
                self.scrollViewDidEndDecelerating(self.mainView!);
                self.mainView?.userInteractionEnabled = true;
        }
    }
    
    //MARK: 创建主试图
    private func createMainView(){
        self.mainView = UIScrollView(frame: CGRectMake(0, 0, self.frame.size.width+5, self.frame.size.height))
        self.mainView?.delegate = self
        self.mainView?.pagingEnabled = true
        self.mainView?.showsHorizontalScrollIndicator = false
        self.mainView?.contentSize = CGSizeMake(self.frame.size.width * CGFloat(3) + CGFloat(5)*CGFloat(3) , self.frame.size.height)
        self.addSubview(self.mainView!)
        
        for index in 0...2{
            let imageView:UIImageView = self.createImageView(index)
            switch index{
            case 0:
                self.pageViewLeft = imageView
            case 1:
                self.pageViewCenter = imageView
            case 2:
                self.pageViewRight = imageView
            default:
                break
            }
        }
        self.pageViewCenter?.tag = 0
        //将滚动试图移动到中心
        self.mainView?.contentOffset = CGPointMake(self.mainView!.frame.size.width, 0)
    }
    
    //MARK: 创建内容试图
    private func createImageView(index:Int)->UIImageView{
        let imageView:UIImageView = UIImageView(frame: CGRectMake(CGFloat(index) * (self.frame.size.width + CGFloat(5)), 0, self.frame.size.width, self.frame.size.height))
        imageView.layer.masksToBounds = true
        imageView.userInteractionEnabled = true;
        imageView.addGestureRecognizer(UITapGestureRecognizer(target: self, action: "tapAction:"));
        self.mainView?.addSubview(imageView)
        mViews?.addObject(imageView)
        return imageView
    }
    
    //MARK: ACTION
    func longPan(longPree:UILongPressGestureRecognizer){
        switch longPree.state{
        case UIGestureRecognizerState.Began:
            self.isStopRoll = true;
        case UIGestureRecognizerState.Ended:
            self.isStopRoll = false;
        default:
            break
        }
    }
    
    func tapAction(tap:UITapGestureRecognizer){
        let imageView:UIImageView = tap.view as! UIImageView;
        let tag:Int = imageView.tag;
        self.csDelegate?.cscrollViewOnClick!(tag);
    }
    
    
    //MARK: 配置图片
    private func configImageView(){
        //判断个数
        self.pageViewCenter?.kf_setImageWithURL(NSURL(string: self.imagesUrls.objectAtIndex(0) as! String)!, placeholderImage: placeHoderImage)
        self.mainView?.userInteractionEnabled = true
        if self.imagesUrls.count == 1{
            self.mainView?.userInteractionEnabled = false
        }else if self.imagesUrls.count == 2{
            self.pageViewLeft?.kf_setImageWithURL(NSURL(string: self.imagesUrls.objectAtIndex(1) as! String)!, placeholderImage: placeHoderImage)
            self.pageViewRight?.kf_setImageWithURL(NSURL(string: self.imagesUrls.objectAtIndex(1) as NSString), placeholderImage: placeHoderImage)
        }else{
            self.pageViewLeft?.kf_setImageWithURL(NSURL(string: self.imagesUrls.objectAtIndex(self.imagesUrls.count-1) as NSString), placeholderImage: placeHoderImage)
            self.pageViewRight?.kf_setImageWithURL(NSURL(string: self.imagesUrls.objectAtIndex(1) as NSString), placeholderImage: placeHoderImage)
        }
    }
    //MARK: 配置子试图
    private func configSubViews(){
        self.pageViewCenter!.addSubview(self.subViews.firstObject! as UIView)
        self.pageViewLeft!.addSubview(self.subViews.lastObject as UIView)
        self.pageViewRight!.addSubview(self.subViews.objectAtIndex(1) as UIView)
    }
    
    //MARK: 移动试图向左
    private func allArticlesMoveLeft(pageWidth:CGFloat){
        
        var lastCenterTag:Int = self.pageViewCenter!.tag
        
        var tempView:UIImageView = self.pageViewLeft!
        self.pageViewLeft = self.pageViewCenter
        self.pageViewCenter = self.pageViewRight
        self.pageViewRight = tempView
        
        
        var count = 0;
        if self.isImageSubView! {
            count = self.imagesUrls.count - 1
        }else{
            count = self.subViews.count - 1
        }
        
        if lastCenterTag == count{
            self.pageViewCenter?.tag = 0
        }else{
            self.pageViewCenter?.tag = lastCenterTag + 1;
        }
        //重新附图,中,左图不变,改变右图
        var nextTag:Int = Int(self.pageViewCenter!.tag) + 1;
        
        //更新图
        if !self.isImageSubView!{
            if nextTag == self.subViews.count{
                nextTag = 0
            }
            self.pageViewRight!.addSubview(self.subViews.objectAtIndex(nextTag) as UIView)
        }else{
            if nextTag == self.imagesUrls.count{
                nextTag = 0
            }
            self.pageViewRight?.setImageWithURL(NSURL(string: self.imagesUrls.objectAtIndex(nextTag) as NSString), placeholderImage: placeHoderImage)
        }
        
        
    }
    //MARK: 移动试图向右
    private func allArticlesMoveRight(pageWidth:CGFloat){
        
        var lastCenterTag:Int = self.pageViewCenter!.tag
        
        var tempView:UIImageView = self.pageViewRight!
        self.pageViewRight = self.pageViewCenter
        self.pageViewCenter = self.pageViewLeft
        self.pageViewLeft = tempView
        
        var count = 0
        if self.isImageSubView! {
            count = self.imagesUrls.count - 1
        }else{
            count = self.subViews.count - 1
        }
        
        if lastCenterTag == 0{
            self.pageViewCenter?.tag = count
        }else{
            self.pageViewCenter?.tag = lastCenterTag - 1
        }
        
        //重新附图,中,左图不变,改变右图
        var nextTag:Int = Int(self.pageViewCenter!.tag) - 1
        
        //更新图
        if self.subViews.count != 0{
            if nextTag < 0{
                nextTag = self.subViews.count - 1
            }
            self.pageViewLeft!.addSubview(self.subViews.objectAtIndex(nextTag) as UIView)
        }else{
            if nextTag < 0{
                nextTag = self.imagesUrls.count - 1
            }
            self.pageViewLeft?.setImageWithURL(NSURL(string: self.imagesUrls.objectAtIndex(nextTag) as NSString), placeholderImage: placeHoderImage)
        }
        
    }
    //MARK: 重新设置试图位置
    private func set_frame_center(){
        var x:CGFloat = self.mainView!.frame.size.width
        var y:CGFloat = 0
        var width =  self.pageViewCenter!.frame.size.width
        var height =  self.pageViewCenter!.frame.size.height
        self.pageViewCenter?.frame = CGRectMake(x, y, width, height)
    }
    private func set_frame_Left(){
        let x:CGFloat = 0
        let y:CGFloat = 0
        let width =  self.pageViewLeft!.frame.size.width
        let height =  self.pageViewLeft!.frame.size.height
        self.pageViewLeft?.frame = CGRectMake(x, y, width, height)
    }
    private func set_frame_Right(){
        let x:CGFloat = self.mainView!.frame.size.width * 2
        let y:CGFloat = 0
        let width =  self.pageViewRight!.frame.size.width
        let height =  self.pageViewRight!.frame.size.height
        self.pageViewRight?.frame = CGRectMake(x, y, width, height)
    }
    
    //MARK: 滚动试图代理
    func scrollViewDidEndDecelerating(scrollView: UIScrollView) {
        let pageWidth:CGFloat = scrollView.frame.size.width
        let page:Int = Int(scrollView.contentOffset.x / pageWidth)
        if page == 1{
            //保持在中间不需要移动
            return
        }else if page == 0{
            self.allArticlesMoveRight(pageWidth)
        }else{
            self.allArticlesMoveLeft(pageWidth)
        }
        self.set_frame_center()
        self.set_frame_Right()
        self.set_frame_Left()
        scrollView.setContentOffset(CGPointMake(pageWidth, 0), animated: false)
        self.csDelegate?.cscrollViewPageChange!(self.pageViewCenter!.tag)
    }
    
}
