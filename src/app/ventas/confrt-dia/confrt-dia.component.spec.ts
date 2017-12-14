import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ConfrtDiaComponent } from './confrt-dia.component';

describe('ConfrtDiaComponent', () => {
  let component: ConfrtDiaComponent;
  let fixture: ComponentFixture<ConfrtDiaComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ConfrtDiaComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ConfrtDiaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
