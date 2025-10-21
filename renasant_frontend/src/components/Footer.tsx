export default function Footer(){
  return (
    <footer className="bg-[#114b66] text-white">
      <div className="max-w-6xl mx-auto px-6 py-6">
        <div className="flex flex-col gap-4">
          <div className="flex items-center gap-4 text-sm">
            <div className="flex items-center gap-3">
              <img src="/assets/ehl.svg" alt="Equal Housing Lender" className="h-10 w-auto" />
              <img src="/assets/fdic.svg" alt="Member FDIC" className="h-10 w-auto" />
            </div>
            <span className="uppercase tracking-wide text-xs">© RENASANT BANK</span>
          </div>

          <div className="flex flex-wrap gap-3 text-sm">
            <a className="underline" href="#">Accessibility</a>
            <span>|</span>
            <a className="underline" href="#">Mobile Privacy</a>
            <span>|</span>
            <a className="underline" href="#">Privacy Statement</a>
            <span>|</span>
            <a className="underline" href="#">Digital Banking Agreement</a>
          </div>
        </div>
      </div>
    </footer>
  )
}